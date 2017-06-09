# -*- coding: utf-8 -*-
from __future__ import unicode_literals
from django.contrib.admin import ModelAdmin, TabularInline, site
from django.contrib import admin

#from mongoadmin import site, DocumentAdmin
#
#from models import Modelo, ModeloCampos
#
#
#class ModeloCamposAdminInline(TabularInline):
#    model                   = ModeloCampos
#    extra                   = 0
#    sortable_field_name     = 'nome'
#
#class ModeloAdmin(Modelo):
#    list_display = ['nome', 'descricao']
#    list_filter = ('nome', 'descricao')
#    search_fields = ('nome', 'descricao')
#    ordering = ['nome']
#    save_as = True
#    inlines = [ModeloCamposAdminInline, ]
#
#
#site.register(Modelo, ModeloAdmin)
