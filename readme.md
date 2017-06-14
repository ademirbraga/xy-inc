****************************
XY-INC Backend as a Service
****************************

Backend as a Service desenvolvido em PHP e PostgreSql

- PHP 5.6.x
- PosgreSql 9.5.7
- Apache 2.4.18
- `Framework CodeIgniter-3.1 <https://codeigniter.com/>`_


****************************
Api
****************************
Por padrão temos duas rotas de recursos

- **/entity** : Rota para os modelos
- **/api** : Rota padrão para os registros dos novos modelos

Todas as rotas seguem o padrão REST, sendo GET para buscar dados, POST para criar, etc.

********************************************************
Listando as entidades existentes
********************************************************

```GET /entity```
```json
{
    "status": "success",
    "meesage": "2 modelo(s) encontrado(s).",
    "dados": [
        {
            "id_modelo": 26,
            "nome_modelo": "product",
            "descricao": null,
            "ativo": true,
            "fields": [
                {
                    "id_modelo_input": 36,
                    "id_modelo": 26,
                    "nome": "nome",
                    "type": "string",
                    "required": true,
                    "tamanho": 100,
                    "ativo": true,
                    "unico": false
                },
                {
                    "id_modelo_input": 35,
                    "id_modelo": 26,
                    "nome": "valor",
                    "type": "decimal",
                    "required": true,
                    "tamanho": 10,
                    "ativo": true,
                    "unico": false
                }
            ]
        },
        {
            "id_modelo": 27,
            "nome_modelo": "client",
            "descricao": null,
            "ativo": true,
            "fields": [
                {
                    "id_modelo_input": 38,
                    "id_modelo": 27,
                    "nome": "telefone",
                    "type": "string",
                    "required": false,
                    "tamanho": 20,
                    "ativo": true,
                    "unico": false
                },
                {
                    "id_modelo_input": 37,
                    "id_modelo": 27,
                    "nome": "nome",
                    "type": "string",
                    "required": true,
                    "tamanho": 100,
                    "ativo": true,
                    "unico": false
                }
            ]
        }
    ]
}
```


****************************
Criando uma nova entidade
****************************
```POST /entity```
```json
{
	"nome_modelo": "order",
	"fields": [
		{
			"nome": "codigo",
			"type": "string",
			"tamanho": "100",
			"required": true,
			"unico": true
		},
		{
			"nome": "valor",
			"type": "decimal",
			"tamanho": "",
			"required": true,
			"unico": false
		}
	]
}
  ```
****************************
Deletando uma entidade
****************************

```DELETE /entity/123```

Será retornado um código http 204 informado que a operação ocorreu com sucesso.

Utilizando um endpoint dinâmico

Utilizaremos o exemplo criado acima, usuario.

********************************************************
Listando todos os registros de um modelo
********************************************************
```GET /api/product``` e será retornado um código http 200 (OK)

********************************************************
Criando um novo produto
********************************************************

```POST /api/product``` e será retornado um código http 200 (Created)
```json
{
  "nome": "Produto x",
  "valor": "9887.65"
}
```
Obs.: Caso os dados enviados não estejam de acordo com o schema informado, será retornado um código 422 e a mensagem de erro de validação.

****************************
Buscando um produto pelo ID
****************************
```GET /api/product/124``` e será retornado um código http 200 (OK)

****************************
Exempo de retorno:
****************************
```json
{
    "status": "success",
    "message": "1 registro(s) encontrado(s)",
    "dados": {
        "id_product": 6,
        "valor": "9887.65",
        "nome": "Produto x"
    }
}
```
****************************
Atualizando um produto
****************************

```PUT api/usuario/123``` e será retornado um código http 200 (OK)
```json
{
  "nome": "mais um produto xxx",
  "valor": "9887.65"
}
```

Obs.: O verbo PATCH não foi implementado sendo necessário sempre atualizar o objeto inteiro via PUT.

****************************
Deletando um produto
****************************
```DELETE /api/usuario/123``` e será retornado um código http 200 (OK)

****************************
Instalação
****************************

Clone este projeto:
- git clone git@github.com:ademirbraga/xy-inc.git
- crie um virtualhost para xyinc.dev, além de ter previamente instalado:
- PHP 5.6.x
- PosgreSql 9.5.7
- Apache 2.4.18
- Execute o arquivo ```/application/config/xyinc.sql``` que contém informações necessárias para a utilização de autenticações dos serviços REST.

****************************
Executando
****************************
- Abra o browser no seguinte endereço http://xyinc.dev
- **Usuário**: admin@admin.com
- **Senha**: password

Para utilizar os serviços disponibilizados, no **Postman** por exemplo, é preciso ativar **Authorization** com o **type** Basic com os dados de acesso informados acima.

****************************
Testes Unitários
****************************
Para executar os testes unitários é necessário realizar a instalação do Framework de testes unitários PHPUnit.

- `$ wget https://phar.phpunit.de/phpunit-4.8.9.phar`
- `$ chmod +x phpunit-4.8.9.phar`
- `$ sudo mv phpunit-4.8.9.phar /usr/local/bin/phpunit`
- `$ phpunit --version`

**Observações:**
Um arquivo sobre a cobertura de código será armazenado no diretório: ```/application/tests/build/coverage/views```

Para executar os testes unitários...
***************************************************
- `$ cd application/tests`
- `$ phpunit --debug  modules/modelo/models/Modelo_model_test.php`


****************************
Testes de Processos
****************************
Para executar os testes de processos é necessário instalar o PhantomJS e ou CasperJs.

- `PhantomJS <https://gist.github.com/julionc/7476620>`_

- `CasperJS <http://docs.casperjs.org/en/latest/installation.html>`_

Para executar os testes de processos..
***************************************************

- $ cd application/testes-processos/cadastros
- $ casperjs cadastro.js

Apósa a execução dos testes de processos, algumas imagens (print's) das telas serão armazenados no diretório ```/application/testes-processos/cadastros``` visto que atualmente existe apenas uma automação desenvolvida
**Observação**

Os testes desenvolvidos com o CasperJS foram apenas iniciados, executando o login e alguma outra operação simples. No entanto é possível simular inúmeros comportamentos tanto de tela quanto de processos.
