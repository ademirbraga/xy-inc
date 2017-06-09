# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.contrib.auth.models import User
from django.db import models
#from mongoengine import Document, EmbeddedDocument, fields
#
#
#class Modelo(Document):
#    nome         = fields.StringField(required=True)
#    descricao    = fields.StringField(required=True, null=True)
#    campos       = fields.ListField(fields.EmbeddedDocumentField(ModeloCampos))
#
#    def __unicode__(self):
#        return u'%s' % self.nome
#
#
#class ModeloCampos(EmbeddedDocument):
#    nome  = fields.StringField(required=True)
#    tipo  = fields.DynamicField(required=True)
#
#    def __unicode__(self):
#        return u'%s' % self.nome


#from mongoengine import *
#from settings import MONGO_DATABASE_NAME
#
#connect(MONGO_DATABASE_NAME)
#
#class Post(Document):
#    title = StringField(max_length=120, required=True)
#    content = StringField(max_length=500, required=True)
#    last_update = DateTimeField(required=True)
