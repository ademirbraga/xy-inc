# -*- coding: utf-8 -*-
from __future__ import unicode_literals
from django.contrib.admin import ModelAdmin, TabularInline, site
from django.contrib import admin
from models import Modelo, ModeloInput


class ModeloInputAdminInline(TabularInline):
    model = ModeloInput
    extra = 0
    sortable_field_name = 'nome'
    fk_name = 'modelo'


class ModeloAdmin(ModelAdmin):
    list_display = ['nome', 'ativo']
    list_filter = ('nome', 'ativo')
    search_fields = ('nome', 'ativo')
    ordering = ['nome']
    save_as = True
    inlines = [
        ModeloInputAdminInline
    ]
site.register(Modelo, ModeloAdmin)
