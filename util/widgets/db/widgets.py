#coding:utf-8
import simplejson
from settings import STATIC_URL, LANGUAGE_CODE
from django.utils.safestring import mark_safe
from django.forms import TextInput
MONEY_ATTRS = ['symbol', 'showSymbol', 'symbolStay', 'thousands', 'decimal', 'precision', 'defaultZero', 'allowZero', 'allowNegative']

class ColorPickerWidget(TextInput):
    class Media:
        css = {'all': ('%sjs/jPicker/css/jPicker-1.1.6.min.css' % STATIC_URL, '%sjs/jPicker/jPicker.css' % STATIC_URL,)}
        js = ('%sjs/jPicker/jpicker-1.1.6.js' % STATIC_URL, '%sjs/jquery/jquery-1.7.2.min.js' % STATIC_URL,)

    def __init__(self, language=None, attrs=None):
        self.language = language or LANGUAGE_CODE[:2]
        super(ColorPickerWidget, self).__init__(attrs=attrs)

    def render(self, name, value, attrs=None):
        rendered = super(ColorPickerWidget, self).render(name, value, attrs)
        return rendered + mark_safe(
            u'''<script type="text/javascript">django.jQuery(document).ready(function () { django.jQuery('#id_%s').jPicker({ window:{ expandable:true, position:{ x:'screenCenter', y:'center' }, title:'Escolha a cor desejada' }, localization:{ text:{ title:'Arraste para escolher uma cor', newColor:'nova', currentColor: 'atual', ok:'OK', cancel:'Cancelar' }, tooltips:{ colors:{ newColor:'Nova cor - Pressione "OK" para confirmar', currentColor:'Clique aqui para reverter para a cor anterior' }, buttons:{ ok:'Confirmar a cor selecionada', cancel:'Cancelar e reverter para a cor original' }, hue:{ radio:'Selecionar modo "Hue"', textbox:'Entre com o valor "Hue" (0-360��)' }, saturation:{ radio:'Selecionar modo "Satura����o"', textbox:'Entre com o valor "Satura����o" (0-100%%)' }, value:{ radio:'Selecionar modo "Valor"', textbox:'Entre com o valor (0-100%%)' }, red:{ radio:'Selecionar modo "Vermelho"', textbox:'Entre com o valor do vermelho (0-255)' }, green:{ radio:'Selecionar modo "Verde"', textbox:'Entre com o valor do verde (0-255)' }, blue:{ radio:'Selecionar modo "Azul"', textbox:'Entre com o valor do azul (0-255)' }, hex:{ textbox:'Entre com uma cor em hexadecimal (#000000-#ffffff)', alpha:'Entre com um valor para o canal "Alpha" (#00-#ff)' } } } }); });</script>''' % name)

class ZMoneyWidget(TextInput):
    symbol = None
    show_symbol = None
    symbol_stay = None
    class Media: js = ('%sjs/jquery.maskMoney.js' % STATIC_URL,)
    def __init__(self, symbol, show_symbol, symbol_stay, **kwargs):
        if symbol: self.symbol = symbol
        if show_symbol is not None: self.show_symbol = show_symbol
        if symbol_stay is not None: self.symbol_stay = symbol_stay
        super(ZMoneyWidget, self).__init__(kwargs)

    def render(self, name, value, attrs=None):
        value = str(value).replace(',','-').replace('.',',').replace('-','.')
        rendered = super(ZMoneyWidget, self).render(name, value, attrs)
        attrs = {'decimal':','}
        print self.symbol == ''
        symbol = self.symbol if self.symbol is not None else ''
        attrs.update({'symbol':symbol})
        if self.show_symbol is not None: attrs.update({'showSymbol':self.show_symbol})
        if self.symbol_stay is not None: attrs.update({'symbolStay':self.symbol_stay})
        for k,v in self.attrs:
            if k in MONEY_ATTRS: attrs.update({k: v})
        jq = mark_safe(u'''<script type="text/javascript">django.jQuery(document).ready(function () {django.jQuery('#id_%s').maskMoney(%s)})</script>''' % (name, simplejson.dumps(attrs)))
        return rendered + jq

class ZMaskedWidget(TextInput):
    mask = None
    class Media: js = ('%sjs/jquery.maskedinput-1.3.min.js' % STATIC_URL,)
    def __init__(self, mask, **kwargs):
        self.mask = mask
        super(ZMaskedWidget, self).__init__(kwargs)

    def render(self, name, value, attrs=None):
        rendered = super(ZMaskedWidget, self).render(name, value, attrs)
        jq = mark_safe(u'''<script type="text/javascript">django.jQuery(document).ready(function () {django.jQuery('#id_%s').mask('%s')})</script>''' % (name, self.mask))
        return rendered + jq



class ZColorPickerWidget(TextInput):
    class Media:
        css = {'all': ('%sjs/jPicker/css/jPicker-1.1.6.min.css' % STATIC_URL, '%sjs/jPicker/jPicker.css' % STATIC_URL,)}
        js = ('%sjs/jPicker/jpicker-1.1.6.js' % STATIC_URL, '%sjs/jquery/jquery-1.7.2.min.js' % STATIC_URL,)

    def __init__(self, language=None, attrs=None):
        self.language = language or LANGUAGE_CODE[:2]
        super(ZColorPickerWidget, self).__init__(attrs=attrs)

    def render(self, name, value, attrs=None):
        rendered = super(ZColorPickerWidget, self).render(name, value, attrs)
        return rendered + mark_safe(
            u'''<script type="text/javascript">django.jQuery(document).ready(function () { django.jQuery('#id_%s').jPicker({ window:{ expandable:true, position:{ x:'screenCenter', y:'center' }, title:'Escolha a cor desejada' }, localization:{ text:{ title:'Arraste para escolher uma cor', newColor:'nova', currentColor: 'atual', ok:'OK', cancel:'Cancelar' }, tooltips:{ colors:{ newColor:'Nova cor - Pressione "OK" para confirmar', currentColor:'Clique aqui para reverter para a cor anterior' }, buttons:{ ok:'Confirmar a cor selecionada', cancel:'Cancelar e reverter para a cor original' }, hue:{ radio:'Selecionar modo "Hue"', textbox:'Entre com o valor "Hue" (0-360��)' }, saturation:{ radio:'Selecionar modo "Satura����o"', textbox:'Entre com o valor "Satura����o" (0-100%%)' }, value:{ radio:'Selecionar modo "Valor"', textbox:'Entre com o valor (0-100%%)' }, red:{ radio:'Selecionar modo "Vermelho"', textbox:'Entre com o valor do vermelho (0-255)' }, green:{ radio:'Selecionar modo "Verde"', textbox:'Entre com o valor do verde (0-255)' }, blue:{ radio:'Selecionar modo "Azul"', textbox:'Entre com o valor do azul (0-255)' }, hex:{ textbox:'Entre com uma cor em hexadecimal (#000000-#ffffff)', alpha:'Entre com um valor para o canal "Alpha" (#00-#ff)' } } } }); });</script>''' % name)