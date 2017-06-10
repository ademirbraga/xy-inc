# -*- coding: utf-8 -*-
from __future__ import unicode_literals
from django.db.models import CharField, ManyToManyField, BooleanField, TextField, ForeignKey, IntegerField, FileField, DateField
from util.models import Padrao
from ckeditor.fields import RichTextField
from settings import TIPOS_INPUTS



class Modelo(Padrao):
    class Meta:
        verbose_name        = u'Modelo'
        verbose_name_plural = u'Modelos'
        db_table            = 'modelo'

    nome                    = CharField(verbose_name=u'Modelo', max_length=150)
    descricao               = RichTextField(verbose_name=u'Descrição', config_name='awesome_ckeditor')
    ativo                   = BooleanField(verbose_name=u'Ativo?', default=True)

    def __unicode__(self):
        return u'%s'%self.nome


class ModeloInput(Padrao):
    class Meta:
        verbose_name        = u'Modelo Input'
        verbose_name_plural = u'Modelos Inputs'
        db_table            = 'modelo_input'

    nome_input              = CharField(verbose_name=u'Input', max_length=150)
    tipo_input              = IntegerField(verbose_name=u'Tipo Input', choices=TIPOS_INPUTS, default=1)
    modelo                  = ForeignKey(Modelo, verbose_name=u'Modelo', related_name='modelo_input')
    required                = BooleanField(verbose_name=u'Obrigatório?', default=False)
    ativo                   = BooleanField(verbose_name=u'Ativo?', default=True)

    def __unicode__(self):
        return u'%s'%self.nome_input
