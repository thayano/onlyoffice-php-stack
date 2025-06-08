# INTEGRAÇÃO DE APP PHP COM ONLYOFFICE

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![ONLYOFFICE](https://img.shields.io/badge/ONLYOFFICE-FF6F3D?style=for-the-badge&logo=onlyoffice&logoColor=white)

### Este projeto implementa uma integração entre uma aplicação PHP e o ONLYOFFICE Document Server utilizando Docker Compose. Ele serve como um guia prático e uma base funcional para integrar a suíte de editores ONLYOFFICE em qualquer aplicação PHP, resolvendo os desafios mais comuns de rede, segurança e callbacks.

## 💻 Avisos

Este projeto foi desenvolvido e testado com as seguintes versões e tecnologias:

-   Linguagem: `PHP 8.2` (via imagem `php:8.2-apache` do Docker)
-   ONLYOFFICE: `Document Server 8.1.0` (ou a versão `:latest` disponível)
-   A integração foi baseada na documentação oficial da API do ONLYOFFICE: [ONLYOFFICE API Documentation](https://api.onlyoffice.com/editors/basic).

## 🚀 Instalação

Para clonar e executar este projeto localmente, você precisará ter o **Docker** e o **Docker Compose** instalados. Siga estas etapas:

**1. Clone o repositório:**
```bash
git clone [https://github.com/thayano/onlyoffice-php-stack.git](https://github.com/thayano/onlyoffice-php-stack.git)
cd onlyoffice-php-stack
```

**2. Inicie os containers:**
Este comando irá construir e iniciar os serviços do PHP e do ONLYOFFICE em segundo plano.
```bash
docker-compose up -d
```

**3. Configure o OnlyOffice:**

Acesse: <a href="http://localhost:8888/" target='_blank' title="Verificar status do ONLYOFFICE">localhost:8888</a>
Execute os 3 comandos no terminal


## ☕ Usando

Após a instalação, a aplicação estará pronta para uso.

1.  Acesse a aplicação PHP no seu navegador: <a href="http://localhost:8000/" target='_blank' title="Acessar Aplicação de Teste">localhost:8000</a>
2.  Clique no nome do arquivo (`teste.docx`) na lista.
3.  Aguarde o carregamento do editor do ONLYOFFICE, que aparecerá na mesma página. Agora você pode editar e salvar o documento.
4.  Um erro bem comun na hora de salvar o arquivo,é o diretório não ter permissão. Caso aconteça, verifique os log do container.

# Arquitetura e Fluxo de Trabalho

O fluxo de trabalho da integração ocorre da seguinte forma:

1. O usuário acessa a página `index.php` em seu navegador.
2. Ele clica no link de um documento para editá-lo.
3. O JavaScript no frontend faz uma chamada `fetch` para o endpoint `api_config.php` no backend.
4. O `api_config.php` gera um objeto de configuração contendo:
   - a URL do documento,
   - a URL de callback,
   - as permissões do usuário e
   - o idioma.
5. Este objeto de configuração é assinado e transformado em um token JWT seguro.
6. O backend responde à chamada `fetch` com a configuração completa em formato JSON, incluindo o token.
7. O JavaScript no frontend recebe a configuração e inicializa o Editor **ONLYOFFICE** em uma `<div>` designada, passando o objeto de configuração.
8. O Editor **ONLYOFFICE**, executando no navegador, usa a `document.url` da configuração para solicitar o arquivo do servidor PHP (através do `download.php`).  
   Essa comunicação ocorre pela rede interna do Docker (usando o endereço `http://meu-app-php/...`).
9. Quando o usuário salva o documento, o **ONLYOFFICE Document Server** processa as alterações e faz uma requisição `POST` para a `callbackUrl` (`save.php`).  
   Essa chamada também ocorre pela rede interna do Docker.
10. O script `save.php`:
    - valida o token JWT recebido,
    - baixa o arquivo atualizado da URL temporária fornecida pelo ONLYOFFICE,
    - corrige a URL para funcionar na rede interna e,
    - finalmente, adiciona o arquivo editado no servidor.


## 📝 Licença

Esse projeto está sob licença. Veja o arquivo [LICENÇA](LICENSE.md) para mais detalhes.
