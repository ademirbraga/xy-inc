#coding:utf-8
import os
from django.core.mail.message import EmailMultiAlternatives
from django.core.paginator import Paginator, EmptyPage, InvalidPage
from django.template.defaultfilters import slugify
import settings, threading
import urllib2,cookielib


from django.conf import settings

EMAIL_SENDER = getattr(settings, 'DEFAULT_FROM_EMAIL', 'EMAIL_HOST_USER')

class EmailThread(threading.Thread):
    def __init__(self, subject, html, body, from_email, recipient_list, headers, fail_silently, ):
        self.subject = subject
        self.body = body
        self.recipient_list = recipient_list
        self.from_email = from_email
        self.fail_silently = fail_silently
        self.html = html
        self.headers = headers
        threading.Thread.__init__(self)
    def run (self):
        msg = EmailMultiAlternatives(self.subject, self.body, self.from_email, self.recipient_list, self.headers)
        if self.html: msg.attach_alternative(self.html, "text/html")
        msg.extra_headers.update(self.headers)
        msg.send(self.fail_silently)

#def send_mail(subject, recipient_list, html, body='', from_email=EMAIL_SENDER, headers=None, fail_silently=False, *args, **kwargs):
#    if not headers: headers = {'Reply-To':','.join(recipient_list)}
#    EmailThread(subject, html, body, from_email, recipient_list, headers, fail_silently).start()

def send_mail(subject,to,html_content):
    from_email = settings.EMAIL_HOST_USER
    text_content = 'Nao responda'
    msg = EmailMultiAlternatives(subject, text_content, from_email, [to])
    msg.attach_alternative(html_content, "text/html")
    msg.send()



def titzr(txt): return slugify(txt).replace('-',' ').title()

def _paginar(request, objeto, num=10):
    paginator = Paginator(objeto, num)
    try: page = int(request.GET.get('page', '1'))
    except ValueError: page = 1
    try: objs = paginator.page(page)
    except (EmptyPage, InvalidPage): objs = paginator.page(paginator.num_pages)
    return objs

'''
Classe para baixar os arquivos da caixa
'''
class Downloads(object):
    def FileFromUrl(self,url, localFileName = None):
        request = urllib2.Request( url)
        response = urllib2.urlopen( request )
        ''' Salvar o arquivo'''
        output = open(localFileName,'wb')
        output.write(response.read())
        output.close()
    def __init__(self):
        self._logged_in = False
        self._cookies = cookielib.CookieJar()
        self._opener = urllib2.build_opener(urllib2.HTTPCookieProcessor(self._cookies))
        urllib2.install_opener(self._opener)



def fetch_resources(uri, rel):
    path = os.path.join(settings.MEDIA_ROOT, uri.replace(settings.MEDIA_URL, ""))
    return path
#    return settings.MEDIA_ROOT



URL_REDIRECT_LOGIN = '/fornecedor/login/'


AMB_PRODUCAO = 1
AMB_HOMOLOGACAO = 2
AMB_TESTES = 3
TIPO_AMBIENTE =(
    (AMB_PRODUCAO,u'PRODUÇÃO'),
    (AMB_HOMOLOGACAO,u'HOMOLOGAÇÃO'),
    (AMB_TESTES,u'TESTES'),
)


TIPO_EMAIL_RENOVACAO_CONTRATO = 1
TIPO_EMAIL_LEMBRETE_REGISTRO_JOGOS = 2
TIPO_EMAIL_CONFIRMACAO_CADASTRO = 3
TIPO_EMAIL = (
    (TIPO_EMAIL_CONFIRMACAO_CADASTRO,u'Confirmação de Cadastro'),
    (TIPO_EMAIL_RENOVACAO_CONTRATO,u'Renovação de Contrato'),
    )