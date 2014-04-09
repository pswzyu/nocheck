#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
import codecs
import csv
import cStringIO
import datetime
from django.core import serializers
from django.db.models.query import QuerySet
from django.http import HttpResponse
from django.utils.timezone import now
import json
from utils import const


def days_ago(days):
  """Midnight **days** days ago."""
  return (now() - datetime.timedelta(days=days)).replace(
      hour=0, minute=0, second=0, microsecond=0)

def object_to_dict(obj, full_dict=False):
  d = json.loads(serializers.serialize('json', [obj]))[0]
  if full_dict or 'fields' not in d:
    return d
  return d['fields']

def objects_to_list(obj):
  if isinstance(obj, list) or isinstance(obj, QuerySet):
    return [object_to_dict(o) for o in obj]
  return [object_to_dict(obj)]

def j(obj):
  return HttpResponse(DateTimeJSONEncoder().encode(obj),
                      mimetype='application/json; charset=utf-8')

class DateTimeJSONEncoder(json.JSONEncoder):
  def default(self, obj):
    if isinstance(obj, datetime.datetime):
      return obj.strftime(const.CHECKEE_TIMEFMT)
    else:
      return super(DateTimeJSONEncoder, self).default(obj)

class UnicodeCSVWriter:
  def __init__(self, f, dialect=csv.excel, encoding="utf-8-sig", **kwds):
    self.queue = cStringIO.StringIO()
    self.writer = csv.writer(self.queue, dialect=dialect, **kwds)
    self.stream = f
    self.encoder = codecs.getincrementalencoder(encoding)()

  def _encode(self, s):
    if isinstance(s, datetime.datetime):
      return s.strftime(const.CHECKEE_TIMEFMT).encode("utf-8")
    if isinstance(s, float):
      return ("%d" % s).encode("utf-8")
    if s:
      return s.encode("utf-8")
    return None

  def writerow(self, row):
    self.writer.writerow([self._encode(s) for s in row])
    data = self.queue.getvalue()
    data = data.decode("utf-8")
    data = self.encoder.encode(data)
    self.stream.write(data)
    self.queue.truncate(0)

  def writerows(self, rows):
    for row in rows:
      self.writerow(row)
