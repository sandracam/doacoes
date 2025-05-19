# Sistema de Doação de Livros

Este é um projeto acadêmico desenvolvido para a disciplina de Programação Orientada a Objetos, do curso de Análise e Desenvolvimento de Sistemas da Faculdade Laboro.

## Equipe:
- Sandra Regina Câmara
- Roberto Melo
- Leonardo Vasconcelos
- Thiago Gabriel

## Sobre o Projeto

O Sistema de Doação de Livros é uma plataforma web que permite aos usuários doarem e solicitarem livros. O sistema facilita a troca de livros entre pessoas, promovendo a cultura e o acesso à leitura.

## Funcionalidades Principais

- Cadastro e autenticação de usuários
- Doação de livros
- Solicitação de livros disponíveis
- Categorização de livros
- Gerenciamento de estado de conservação
- Sistema de status para doações

## Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor Web (Apache/Nginx)
- XAMPP (recomendado para ambiente de desenvolvimento)

## Instalação

1. Clone este repositório para seu servidor web local:
```bash
git clone [URL_DO_REPOSITÓRIO]
```

2. Configure seu servidor web (XAMPP) para apontar para o diretório do projeto

3. Importe o banco de dados:
   - Acesse o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um novo banco de dados
   - Importe o arquivo `database.sql` que está na pasta `database`

4. Configure a conexão com o banco de dados:
   - Abra o arquivo `config/database.php`
   - Atualize as credenciais do banco de dados conforme seu ambiente

5. Acesse o sistema através do navegador:
```
http://localhost/doacoes
```

## Estrutura do Projeto

```
doacoes/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   └── footer.php
├── livros.php
├── login.php
├── register.php
└── solicitar_doacao.php
```

## Tecnologias Utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5

## Contribuição

Este é um projeto acadêmico e não está aberto para contribuições externas.

## Informações do projeto

- Curso: Análise e Desenvolvimento de Sistemas
- Instituição: Faculdade Laboro
- Disciplina: Programação Orientada a Objetos 