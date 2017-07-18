**xy-inc**
----------

**xy-inc** é uma API Rest para manipulação de pontos de interesse (POIs) utilizando o framework Express (NodeJS) e MongoDB.

**Requisitos**

[NodeJS](https://nodejs.org/en/download/ "NodeJS")
    
[MongoDB](https://www.mongodb.org/downloads "MongoDB")
    
[Yarn](https://yarnpkg.com/lang/en/docs/install/ "Yarn")

**Instalação**

Primeiramente é necessário fazer o download dos arquivos:

git clone https://github.com/ademirbraga/xy-inc
Navegue até o diretório raiz /xy-inc e execute o seguinte comando para instalar as dependências:

    yarn install

Para facilitar a instalação do MongoDB é recomendado que seja utilizado o docker. Caso tenha o docker já instalado, navegue até o diretório raiz da aplicação e execute o comando:

docker-compose up

Para mais detalhes de uma instalação completa do MongoDB clique [aqui](https://docs.mongodb.com/manual/administration/install-community/).


**Iniciar**

Para iniciar a aplicação basta rodar o seguinte comando:

**yarn start**

Por padrão o serviço é inicializado na porta 3000.

![enter image description here](https://lh3.googleusercontent.com/-kSo7btGlpJE/WWqNyNGIlGI/AAAAAAAAHP4/-K_3R2VP_oUFJ8HsmRtv-B334r-t-tL7wCLcBGAs/s0/image3.png "image3.png")

**Serviços Disponíveis**

| Método  | URL                  | Parâmetros                                  | Descrição                                                                            |
|---------|----------------------|---------------------------------------------|--------------------------------------------------------------------------------------|
| GET     | /pois                |                                             | Lista todos os pontos de interesse cadastrados.                                      |
| GET     | /locations   |*Query String*:?x=:x&y=:y&d_max=:max                 | Busca todos os pontos de interesse (POIs) próximos a determinada ponto de referência.|        
| POST    | /pois        |*JSON*:{"name": "POI Name","coordinates": [10, 20]}  | Cadastra um novo ponto de interesse (POI).|
| PUT     | /pois/:id    |*JSON*:{"name": "POI Atualizado","coordinates": [20, 30]} | Atauliza um Poi existente| 
| DELETE  | /pois/:id    |                                                     | Remove um Poi informado|
 
**Tipos de Respostas:**

| Código | Nome                   | Descrição                                                            |
|--------|------------------------|----------------------------------------------------------------------| 
|200     | OK                     | Indica que a operação foi realizada com sucesso.                     |
|201     | CREATED                | Indica quem um registro foi criado com sucesso.                      |
|204     | No Content             | Nenhum conteúdo retornado |
|400     | Bad Request            | Indica que os parâmetros fornecidos estão incorretos.                |
|404     | Not Found              | Indica que o recurso solicitado não foi localizado.                  |   
|409     | Conflict           | Indica que existe algum conflito |
|500     | Internal Server Error  | Indica que ocorreu algum erro interno no processamento da requisição |
 


**Testes**

Os testes podem ser executados através dos comandos:

    yarn test
    yarn test:unit
    yarn test:integration

Para gerar cobertura:

    yarn test:unit-coverage

![enter image description here](https://lh3.googleusercontent.com/-B9ct88-XoTc/WW1_-sn328I/AAAAAAAAHRc/QM1HZuBVmGAq_zuf7wD8TBlkalW1yG9fwCLcBGAs/s0/resumo.png "resumo.png")

Após a execução dos testes será gerado uma pasta coverage com o relatório completo dos testes realizados.

![enter image description here](https://lh3.googleusercontent.com/-U13X2SsrBwI/WW2ASXobGZI/AAAAAAAAHRk/jQYwyjIXUj8RMgHim_5cDTC5yyic9Vi1ACLcBGAs/s0/coverage.png "coverage.png")


**Referências**

[Test a node restful api with mocha and chai](https://scotch.io/tutorials/test-a-node-restful-api-with-mocha-and-chai#toc-mocha-testing-environment)

[Chai Assertion Library](http://chaijs.com/)

[Istanbul - JavaScript test coverage](https://istanbul.js.org/)

[Supertest - HTTP assertions](https://www.npmjs.com/package/supertest)

[BabelJs](https://blog.tecsinapse.com.br/utilizando-es6-no-node-js-com-babel-js-430346d68794)
