#coding=utf-8

from django.db.models import Model,DateTimeField

class Padrao(Model):
    class Meta:
        abstract                    = True
    data_cadastro                   = DateTimeField(verbose_name=u'Data de Cadastro',auto_now_add=True)
