#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:


VISA_TYPES = (
  ('B1', 'B1 Visa'),
  ('B2', 'B2 Visa'),
  ('F1', 'F1 Visa'),
  ('F2', 'F2 Visa'),
  ('H1', 'H1 Visa'),
  ('H4', 'H4 Visa'),
  ('J1', 'J1 Visa'),
  ('J2', 'J2 Visa'),
  ('L1', 'L1 Visa'),
  ('L2', 'L2 Visa'),
)

APPLICATION_TYPE = (
  ('New', 'New Visa Application'),
  ('Renewal', 'Visa Renewal'),
)

APPPLICATION_STATUS = (
  ('Pending', 'Pending'),
  ('Clear', 'Clear'),
  ('Reject', 'Rejected')
)

FETCHER_USER_AGENT = ('Mozilla/5.0 (compatible; socialbase; checkcheck;'
                      ' +http://socialbase.cn')
CHECKEE_TIMEFMT = '%Y-%m-%d'
