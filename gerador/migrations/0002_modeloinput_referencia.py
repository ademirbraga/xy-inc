# -*- coding: utf-8 -*-
# Generated by Django 1.11.2 on 2017-06-10 01:55
from __future__ import unicode_literals

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('gerador', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='modeloinput',
            name='referencia',
            field=models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.CASCADE, related_name='modelo_input_referencia', to='gerador.Modelo', verbose_name='Refer\xeancia'),
        ),
    ]
