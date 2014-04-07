#!/usr/bin/python
# -*- coding: UTF-8 -*-
# vim:set shiftwidth=2 tabstop=2 expandtab textwidth=79:
from django.conf import settings
from django.conf.urls import patterns, url
from django.conf.urls.static import static

urlpatterns = patterns('',
  url(r'^$', 'check.views.dashboard', name='dashboard'),
  url(r'^details/(?P<year>\d+)/(?P<month>\d+)/$',
      'check.views.details', name='details'),
  url(r'^visa/(?P<visa_type>.*)/$',
      'check.views.visa_type_details', name='visa_type_details'),
  url(r'^raw_data/$', 'check.views.raw_data', name='raw_data'),
  url(r'^raw_data/download/$', 'check.views.download_raw_data',
      name='download_raw_data'),
  url(r'^estimate/$', 'check.views.estimate', name='estimate'),
  url(r'^estimate/ajax/$', 'check.views.estimate_ajax', name='estimate_ajax'),
  url(r'^about/$', 'check.views.about', name='about'),
  url(r'^similar/$', 'check.views.similar', name='similar'),
) + static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
