#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
from django.db import models
from django.utils.timezone import now
from django.utils.translation import ugettext_lazy as _
from utils import const


class Checkee(models.Model):
  checkee_caseid = models.FloatField(null=True, blank=True)
  nickname = models.CharField(null=True, blank=True, max_length=64)
  visa_type = models.CharField(max_length=4, choices=const.VISA_TYPES)
  checked_at = models.DateTimeField(default=now,
                                    verbose_name=_('Check date'))
  cleared_at = models.DateTimeField(null=True, blank=True,
                                    verbose_name=_('Final decision date'))
  consulate = models.CharField(null=True, blank=True, max_length=128,
                               verbose_name=_('Consulate'))
  major = models.CharField(null=True, blank=True, max_length=256,
                           verbose_name=_('Major'))
  application_status = models.CharField(max_length=8,
                                        choices=const.APPPLICATION_STATUS)
