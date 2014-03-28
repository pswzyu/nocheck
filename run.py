#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
import os
os.environ.setdefault("DJANGO_SETTINGS_MODULE", "checkcheck.settings")


from utils import fetch

fetch.fetch_recent_checkee(18)
