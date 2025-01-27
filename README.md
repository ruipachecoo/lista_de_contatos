# Lista de Contatos

Uma aplicação web que gerencia pessoas e seus contatos (telefone, e-mail, WhatsApp e similares). O projeto inclui uma API REST no back-end para manipulação dos dados e um front-end para interação com a API.

---

## Requisitos para Configuração

1. **Docker** instalado.
2. **Docker Compose** instalado.

---

## Configuração e Execução

Execute os comandos a seguir:

1. **Build e inicialização dos containers:**

   ```bash
   docker-compose up --build
   ```

2. **Acessar a aplicação:**

   Acesse [http://localhost:8080](http://localhost:8080) ou [http://0.0.0.0:8080](http://0.0.0.0:8080) em seu navegador.

3. **Encerrar os containers:**

   Quando não precisar mais da aplicação, finalize os containers com:

   ```bash
   docker-compose down
   ```

---

## Como Funciona

1. **Front-End:**
   - O arquivo `index.html` apresenta a interface para adicionar, visualizar, editar e excluir pessoas e seus contatos.
   - `style.css` garante uma aparência simples e responsiva.
   - `script.js` faz requisições para a API utilizando `fetch`.

2. **Back-End:**
   - `pessoas.php` e `contatos.php` gerenciam as rotas da API para operações de CRUD (Create, Read, Update, Delete).
   - `db.php` fornece a conexão ao banco de dados MariaDB (configurado pelo `docker-compose.yml`).

3. **Banco de Dados:**
   - Um container MariaDB é configurado para armazenar as tabelas e dados da aplicação.
   - O script `init.sql` inicializa as tabelas e dados básicos ao subir o container pela primeira vez.

---

## Endpoints da API

- **`/pessoas`**:
  - `GET`: Retorna todas as pessoas.
  - `POST`: Adiciona uma nova pessoa.
  - `DELETE`: Remove uma pessoa.

- **`/contatos`**:
  - `GET`: Retorna os contatos de uma pessoa.
  - `POST`: Adiciona um novo contato.
  - `PUT`: Atualiza um contato existente.

---

## Logs e Depuração

Os logs do Apache e do PHP são armazenados no diretório `./logs`. Eles podem ser acessados para verificar erros ou mensagens de depuração.

---

## Observações

- Garanta que as portas 8080 (aplicação) e 3306 (banco de dados) estejam livres antes de iniciar os containers.
- Caso precise adicionar ou modificar o banco de dados, ajuste o arquivo `init.sql` e reconstrua os containers.
