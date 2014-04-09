#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
import datetime
import math
import logging
from utils import const


logger = logging.getLogger(__name__)


def split_aggregate(date_tuples, date_interval=3, start_date=None,
                    as_dict=False, include_zero=True, use_none=False):
  """Split by date and aggregate by date_interval.

  Args:
    date_tuples: [(datetime.datetime, int_value), ...]
    start_date: Optional start date, DateTime
    date_interval: Grin.
    as_dict: Return dictionary.
    include_zero: Include zero.
    use_none: Use None instead of zero.
  """
  # Get start and end date.
  if not date_tuples:
    if as_dict:
      return {}
    return [["", 0]]

  sorted_date = sorted(dict(date_tuples).keys())
  if not start_date:
    start_date = sorted_date[0]
  stop_date = sorted_date[-1]

  days = (stop_date - start_date).days + 1
  slices = int(math.ceil(1.0 * days / date_interval))
  r = []
  for s in xrange(0, slices + 1):
    stop_ = start_date + datetime.timedelta(days=(s+1)*date_interval)
    start_ = start_date + datetime.timedelta(days=s*date_interval)
    key_ = start_.strftime(const.CHECKEE_TIMEFMT)
    v = [v for v in date_tuples if v[0] < stop_ and v[0] >= start_]
    if v:
      r.append([key_, sum([v_[1] for v_ in v]) / len(v)])
    else:
      if include_zero:
        if use_none:
          r.append([key_, None])
        else:
          r.append([key_, 0])

  if as_dict:
    return dict(r)

  return sorted(r)
