#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
import collections
from check.models import Checkee
import datetime
from django.db.models import Count, Q
from django.http import HttpResponse
from django.shortcuts import render_to_response
from django.views.decorators.cache import cache_page
from django.views.decorators.vary import vary_on_headers
import logging
from scipy import stats
from utils import (const, DateTimeJSONEncoder, dataprocessing, j, days_ago,
                   UnicodeCSVWriter)

logger = logging.getLogger(__name__)

@vary_on_headers('Accept-Language')
@cache_page(60*60)
def dashboard(request):
  # Aggregate data.
  # Recent 90 days trend
  start_date = days_ago(90)
  base_ = Checkee.objects.filter(checked_at__gt=(start_date))
  raw_v_data = {}  # Raw cleared data
  raw_s_data = {}  # Raw serializable data
  aggr_plot_data = {}
  aggr_data = {}
  for visa_type_ in const.VISA_TYPES:
    visa_type = visa_type_[0]
    raw_v_data[visa_type] = base_.filter(
        application_status='Clear', visa_type=visa_type)
    raw_s_data[visa_type] = DateTimeJSONEncoder().encode(
        list(base_.filter(visa_type=visa_type).values_list(
            'checked_at', 'cleared_at', 'application_status')))
    # Calculate aggregated data
    v_dat = {}
    c_dates = raw_v_data[visa_type].values_list(
        'cleared_at', 'checked_at')
    all_waits = [(d[0] - d[1]).days for d in c_dates]
    try:
      v_dat['avg_wait'] = sum(all_waits) / len(all_waits)
    except ZeroDivisionError:
      v_dat['avg_wait'] = None
    v_dat['applicants'] = base_.filter(visa_type=visa_type).count()
    v_dat['cleared'] = len(all_waits)
    try:
      v_dat['cleared_ratio'] = '%.2f%%' % (
          100.0 * v_dat['cleared'] / v_dat['applicants'])
    except ZeroDivisionError:
      v_dat['cleared_ratio'] = None
    try:
      v_dat['last_clearance'] = sorted(
          [t[0] for t in c_dates])[-1].strftime(const.CHECKEE_TIMEFMT)
    except IndexError:
      v_dat['last_clearance'] = None
    try:
      v_dat['last_application'] = sorted(
          [t[1] for t in c_dates])[-1].strftime(const.CHECKEE_TIMEFMT)
    except IndexError:
      v_dat['last_application'] = None
    aggr_data[visa_type] = v_dat

    checked_days = [(d.checked_at, d.checked_days.days) for d in
        raw_v_data[visa_type].extra(select={
            'checked_days': 'cleared_at - checked_at'})]
    aggr_plot_data[visa_type] = dataprocessing.split_aggregate(checked_days,
                                                               date_interval=3,
                                                               use_none=True)

  # Calculate weekday distribution
  cleared_dates = [
      d.strftime("%A") for d in base_.values_list('cleared_at', flat=True) if d]
  weekday_dist = collections.defaultdict(int)
  for x in cleared_dates:
    weekday_dist[x] += 1
  weekday = sorted([(k, v) for (k, v) in weekday_dist.items()],
      key=lambda x: x[1], reverse=True)

  # Calculate overall distribution
  last_clear_at = sorted([d.cleared_at.strftime(const.CHECKEE_TIMEFMT)
      for d in base_ if d.cleared_at])[-1]
  last_application_cleared = sorted([
      d.checked_at.strftime(const.CHECKEE_TIMEFMT)
      for d in base_ if d.cleared_at])[-1]
  valid_wait_time = [
      (d.cleared_at - d.checked_at).days for d in base_ if d.cleared_at]
  avg_wait_time = int(round(1.0 * sum(valid_wait_time) / len(valid_wait_time)))

  # Split and show distribution
  total_checked_ = [
      (d['checked_at'], d['total'])
      for d in base_.values('checked_at').annotate(total=Count('checked_at'))
      if d['checked_at']]
  total_cleared_ = [
      (d['cleared_at'], d['total'])
      for d in base_.values('cleared_at').annotate(total=Count('cleared_at'))
      if d['cleared_at']]

  total_checked = dataprocessing.split_aggregate(total_checked_,
                                                 date_interval=1,
                                                 start_date=start_date,
                                                 as_dict=False)
  total_cleared = dataprocessing.split_aggregate(total_cleared_,
                                                 date_interval=1,
                                                 start_date=start_date,
                                                 as_dict=False)
  return render_to_response('dashboard.html', {
      'last_clear_at': last_clear_at,
      'last_application_cleared': last_application_cleared,
      'avg_wait_time': avg_wait_time,
      'raw_data': raw_s_data,
      'aggr_data': aggr_data,
      'aggr_plot_data': DateTimeJSONEncoder().encode(aggr_plot_data),
      'weekday': DateTimeJSONEncoder().encode(weekday),
      'total_checked': DateTimeJSONEncoder().encode(total_checked),
      'total_cleared': DateTimeJSONEncoder().encode(total_cleared),
  })

def details(request, year, month):
  return render_to_response('details.html')

@vary_on_headers('Accept-Language')
@cache_page(60*120)
def similar(request):
  return render_to_response('similar.html')

@vary_on_headers('Accept-Language')
@cache_page(60*120)
def raw_data(request):
  return render_to_response('raw_data.html')

@vary_on_headers('Accept-Language')
@cache_page(60*60)
def download_raw_data(request):
  # Cache raw data for 1 hour.
  response = HttpResponse(mimetype='text/csv; charset=utf-8')
  response['Content-Disposition'] = 'attachment; filename="checkcheck.csv"'
  writer = UnicodeCSVWriter(response)
  all_objs = Checkee.objects.all()
  writer.writerow([
      'Checkee_CaseId', 'VisaType', 'ApplicationDate', 'ClearanceDate',
      'Consulate', 'Major', 'ApplicationStatus'])
  for c in all_objs:
    writer.writerow([
        c.checkee_caseid, c.visa_type, c.checked_at, c.cleared_at,
        c.consulate, c.major, c.application_status])
  return response

def visa_type_details(request, visa_type):
  try:
    days = int(request.GET.get('days'))
  except:
    days = 90

  if days < 10:
    days = 10

  if days > 120:
    days = 120

  base_ = Checkee.objects.filter(
      checked_at__gt=(days_ago(days)),
      visa_type=visa_type
  )
  start_date_ = base_.order_by('checked_at')[0].checked_at
  pending = Q(application_status='Pending')

  raw_data_dist_ = base_.filter(~pending).values_list('checked_at', 'cleared_at')
  raw_data_dist = sorted([
      ((d1 - days_ago(days)).days, (d2 - d1).days)
      for (d1, d2) in raw_data_dist_])

  c = {'visa_type': visa_type}
  total_ = [
      (d['checked_at'], d['total'])
      for d in base_.values('checked_at').annotate(total=Count('checked_at'))]
  cleared_ = [
      (d['checked_at'], d['total'])
      for d in base_.filter(~pending).values('checked_at').annotate(
      total=Count('checked_at'))]
  cleared_ = [
      (d['checked_at'], d['total'])
      for d in base_.filter(~pending).values('checked_at').annotate(
      total=Count('checked_at'))]

  checked_days_ = [(d.checked_at, d.checked_days.days)
      for d in base_.filter(~pending).extra(select={
          'checked_days': 'cleared_at - checked_at'})]

  total = dataprocessing.split_aggregate(total_, start_date=start_date_,
                                         as_dict=True, include_zero=False)
  cleared = dataprocessing.split_aggregate(cleared_, start_date=start_date_,
                                           as_dict=True, include_zero=False)
  checked_days = dataprocessing.split_aggregate(checked_days_,
                                                start_date=start_date_,
                                                as_dict=True,
                                                include_zero=False)
  keys = set(total.keys() + cleared.keys())
  raw_data = []
  norm_data = []
  for k in sorted(keys):
    t1, t2, days_ = (0, 0, None)

    if k in total:
      t1 = total[k]
    if k in cleared:
      t2 = cleared[k]

    raw_data.append([k, (t1 - t2), t2])  # Date, Pending, Cleared
    # Calculate ratio
    t4 = 100 * t2 / t1
    norm_data.append([k, t4])

  # Pending cases data table
  pending_cases = list(base_.filter(pending).order_by(
      'checked_at').values_list('checked_at', 'consulate', 'major'))

  cleared_cases_ = base_.filter(~pending).order_by(
      'checked_at', 'cleared_at').values_list('checked_at', 'cleared_at',
                                              'consulate', 'major')

  cleared_cases = [(ii, jj, (jj-ii).days, kk, ll)
                   for (ii, jj, kk, ll) in cleared_cases_]

  c['raw_data_dist'] = DateTimeJSONEncoder().encode(raw_data_dist)
  c['norm_data'] = DateTimeJSONEncoder().encode(norm_data)
  c['checked_days'] = DateTimeJSONEncoder().encode(checked_days)
  c['pending_cases'] = DateTimeJSONEncoder().encode(pending_cases)
  c['cleared_cases'] = DateTimeJSONEncoder().encode(cleared_cases)
  c['raw_data'] = DateTimeJSONEncoder().encode(raw_data)
  c['days'] = days
  return render_to_response('visa_details.html', c)

def estimate_ajax(request):
  required_fields = ['visa_type', 'application_date']
  for f in required_fields:
    if not request.GET.get(f):
      return j({'status': 'error', 'message': 'All fields are required.'})
  visa_type = request.GET.get('visa_type')
  # Check for application date format
  try:
    application_date = datetime.datetime.strptime(
        request.GET.get('application_date'), const.CHECKEE_TIMEFMT).date()
  except ValueError:
    return j({'status': 'error', 'message': 'Incorrect date format.'})

  # Model the data
  start_date = datetime.date.today() - datetime.timedelta(days=90)
  raw_data = Checkee.objects.filter(
      ~Q(application_status='Pending'),
      checked_at__gt=start_date,
      visa_type=visa_type,
  ).values_list('checked_at', 'cleared_at')
  # Convert to (DATE_SINCE_START_DATE, DAYS) tuple
  # Exclude obvious non-check instances (days<=3)
  threshold = 3
  if request.GET.get('exclude_biased'):
    threshold = sum([(d2 - d1).days
        for (d1, d2) in raw_data]) / len(raw_data) - 10
  d = sorted([
      ((d1.date() - start_date).days, (d2 - d1).days)
      for (d1, d2) in raw_data
      if ((d2 - d1).days > threshold)])

  # Regression
  slope, intercept, r_value, p_value, std_err = stats.linregress(d)
  regression_data = {
    'slope': slope,
    'intercept': intercept,
    'r_value': r_value,
    'p_value': p_value,
    'std_err': std_err
  }
  # Prediction
  prediction_accuracy = True
  p_days = round(slope * (application_date - start_date).days + intercept)
  if abs(r_value) < 0.5:
    prediction_accuracy = False
  if p_days < 0:
    prediction_accuracy = False
    p_days = 1
  p_date = (application_date + datetime.timedelta(days=p_days)).strftime(
      const.CHECKEE_TIMEFMT)
  wait_days = (application_date + datetime.timedelta(days=p_days) -
               datetime.date.today()).days
  progress = (datetime.date.today() - application_date).days * 100.0 / p_days
  return j({
      'status': 'ok', 'raw_data': d, 'regression_data': regression_data,
      'prediction': p_date, 'prediction_accuracy': prediction_accuracy,
      'x_axis': (application_date - start_date).days,
      'p_days': p_days, 'wait_days': wait_days, 'progress': progress})

def estimate(request):
  c = {'visa_type': const.VISA_TYPES}
  return render_to_response('estimate.html', c)

@vary_on_headers('Accept-Language')
@cache_page(60*60)
def about(request):
  return render_to_response('about.html')
