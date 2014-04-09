#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
from bs4 import BeautifulSoup
from check.models import Checkee
import datetime
from django.utils import timezone
import logging
import re
import requests
from utils import const


logger = logging.getLogger(__name__)


def to_localtime(d):
  return timezone.make_aware(d, timezone.get_default_timezone())


def update_checkee(year, month=None):
  """Parse checkee.info page and update them into database.

  Args:
    year: Integer
    month: Integer"""
  def content_list_callback(tag):
    if tag.find_all('a', text='Update'):
      return True

  def parse_record(record):
    """Parse record.

    Returns:
      (case_id, nickname, visa_type, application_type, consulate, major,
       application_status, checked_at, cleared_at) tuple.
      The returned tuple is not parsed."""
    p_r = record.find_all('td')
    if len(p_r) != 11:
      return
    (_, nickname, visa_type, application_type, consulate, major,
        application_status, checked_at, cleared_at, _, _) = [
            t.get_text(strip=True) for t in p_r]
    # Fetch case_id
    if visa_type not in dict(const.VISA_TYPES):
      return
    if application_type not in dict(const.APPLICATION_TYPE):
      return
    if not record.find('a', href=re.compile('casenum')):
      return

    case_id = re.match(r'.*casenum=(\d+)',
                       record.find('a', href=re.compile('casenum'))['href'])
    return (case_id.groups()[0], nickname, visa_type, application_type,
            consulate, major, application_status, checked_at, cleared_at)

  def normalize_record(record):
    (case_id, nickname, visa_type, application_type, consulate, major,
     application_status, checked_at, cleared_at) = record

    if major == 'N/A':
      major = None

    if checked_at == '0000-00-00':
      checked_at = None
    else:
      checked_at = to_localtime(datetime.datetime.strptime(
          checked_at, const.CHECKEE_TIMEFMT))
    if cleared_at == '0000-00-00':
      cleared_at = None
    else:
      cleared_at = to_localtime(datetime.datetime.strptime(
          cleared_at, const.CHECKEE_TIMEFMT))

    return (case_id, nickname, visa_type, application_type, consulate, major,
            application_status, checked_at, cleared_at)

  page_url = 'http://checkee.info/main.php?dispdate=%d' % year
  if month:
    page_url = 'http://checkee.info/main.php?dispdate=%d-%02d' % (year, month)
  logger.warning('Fetching %s...', page_url)
  page = requests.get(page_url,
                      headers={'User-Agent': const.FETCHER_USER_AGENT})
  soup = BeautifulSoup(page.content)
  target_table = soup.find(content_list_callback,  width='98%', cellspacing='0')

  if not target_table:
    return []
  records = target_table.find_all('tr', recursive=False)
  p_ = [parse_record(r) for r in records]
  return [normalize_record(p) for p in p_ if p]


def insert_or_update_record(record):
  """Insert or update record.

  Args:
    record: (case_id, nickname, visa_type, application_type, consulate, major,
             application_status, checked_at, cleared_at) tuple.

  Returns:
    None
  """
  hint = ('case_id', 'nickname', 'visa_type', 'application_type', 'consulate',
          'major', 'application_status', 'checked_at', 'cleared_at')
  update_exclude_fields = ('case_id', )
  d = dict(zip(hint, record))
  c, created_ = Checkee.objects.get_or_create(checkee_caseid=d['case_id'])

  hint_ = [x for x in hint if x not in update_exclude_fields]
  for field_ in hint_:
    setattr(c, field_, d[field_])
  c.save()


def fetch_recent_checkee(month_to_fetch=6):
  """Fetch recent checkee.info data and update our record accordingly."""
  fetch_dates = []
  since = datetime.datetime.now()
  from_year, from_month = since.year, since.month
  for m in xrange(0, month_to_fetch):
    if from_month - m > 0:
      fetch_dates.append((from_year, (from_month - m)))
    else:
      fetch_dates.append(((from_year - 1), (from_month + 12 - m)))

  [[insert_or_update_record(r) for r in update_checkee(*d)]
      for d in fetch_dates]
