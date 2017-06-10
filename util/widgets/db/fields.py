#coding:utf8
from django.forms import CharField

class ZMoneyField(CharField):
    def __init__(self, *args, **kwargs):
        super(ZMoneyField, self).__init__(*args, **kwargs)
    def to_python(self, value):
        np = ['R$', ' ']
        if value:
            for i in np: value = value.replace(i,'')
        value = value.replace('.','').replace(',','.')
        return value

class ZMaskedField(CharField):
    def __init__(self, *args, **kwargs):
        super(ZMaskedField, self).__init__(*args, **kwargs)

    def to_python(self, value):
        np = ['(', ')', '-', '.', '/', ' ']
        if value:
            for i in np: value = value.replace(i,'')
        return value

class ZColorPickerField(CharField):
    def to_python(self, value):
        return value