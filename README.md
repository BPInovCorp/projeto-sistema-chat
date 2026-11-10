# 💬 Inovcorp Chat — Sistema de Chat de Equipa

Sistema de conversação de equipa desenvolvido em **Laravel 11**, com autenticação baseada no modelo `Utilizador`, interface minimalista inspirada no conceito do **ONCE Campfire** (37signals), controlo de acessos baseado em papéis (RBAC), salas de chat de grupo com gestão de membros, mensagens diretas privadas (1:1 / *Pings*) com relações polimórficas, painel administrativo de colaboradores com proteção de concorrência e testes automatizados em **PEST PHP**.

## Índice

- [Funcionalidades Principais](#funcionalidades-principais)
  - [1. Autenticação & Controlo de Acessos (RBAC)](#1-autenticação--controlo-de-acessos-rbac)
  - [2. Salas de Chat & Canais de Grupo](#2-salas-de-chat--canais-de-grupo)
  - [3. Mensagens Diretas Privadas (1:1 / Pings)](#3-mensagens-diretas-privadas-11--pings)
  - [4. Envio Polimórfico de Mensagens (Salas e DMs)](#4-envio-polimórfico-de-mensagens-salas-e-dms)
  - [5. Painel Administrativo de Colaboradores](#5-painel-administrativo-de-colaboradores)
  - [6. Interface Minimalista Estilo ONCE Campfire](#6-interface-minimalista-estilo-once-campfire)

- [Demonstração Visual da Interface](#demonstração-visual-da-interface)
- [Padrões de Desenvolvimento](#padrões-de-desenvolvimento)
- [Requisitos do Sistema](#requisitos-do-sistema)
- [Passos para Instalação](#passos-para-instalação)
- [Testes Automatizados (PEST PHP)](#testes-automatizados-pest-php)
- [Projeto desenvolvido para](#projeto-desenvolvido-para)

## Funcionalidades Principais

### 1. Autenticação & Controlo de Acessos (RBAC)
- **Autenticação com Modelo `Utilizador`:** Extensão de `Illuminate\Foundation\Auth\User`, com hash automático de palavras-passe e persistência de sessões na base de dados.
- **Segregação Estrita de Papéis:**
  - **Administradores (`admin`):** Criação e gestão global de salas, associação e remoção de membros, criação de novos colaboradores, alteração de permissões/estados e desativação de contas;
  - **Utilizadores Regulares (`utilizador`):** Participação nas salas de conversação associadas e troca de mensagens diretas privadas com colegas de equipa.
- **Validação de Contas Inativas (`EnsureUtilizadorAtivo`):** Middleware no grupo `web` que interceta pedidos HTTP autenticados e bloqueia contas com estado `inativo`, terminando a sessão e exibindo mensagem em Português Europeu.
- **Proteção de Rotas Administrativas (`EnsureUtilizadorAdmin`):** Interceta e bloqueia com código HTTP `403 Forbidden` tentativas de acesso por utilizadores comuns a áreas restritas.

### 2. Salas de Chat & Canais de Grupo
- **Listagem de Salas por Utilizador:** O ecrã inicial redireciona o utilizador autenticado para a sua primeira sala disponível (`oldest('id')`), apresentando na barra lateral apenas os canais a que pertence.
- **Criação Segura de Salas:** Formulário protegido por Policy (`SalaPolicy@create`), com validação de nome (aplicando `trim`) e carregamento opcional de imagem de avatar (formatos JPEG, JPG, PNG e WebP até 2MB).
- **Criação Atómica de Salas:** A criação do canal e a associação do Administrador criador ocorrem dentro de uma transação de base de dados (`DB::transaction`), com limpeza de ficheiros órfãos em caso de exceção.
- **Gestão de Membros Exclusiva (`SalaMembroController`):** Adição (`syncWithoutDetaching`) e remoção (`detach`) de colaboradores restritas a administradores (`manageMembers`), com validação que impede adicionar contas inativas e salvaguarda que impede a remoção do criador original da sala.
- **Edição e Eliminação de Salas:** Administradores podem atualizar o nome e avatar da sala, ou eliminá-la (com transação para remoção de mensagens, pivot e registo da sala, seguida da remoção do ficheiro do armazenamento), através de um modal de confirmação do DaisyUI v5.

### 3. Mensagens Diretas Privadas (1:1 / Pings)
- **Fila de Pings na Barra Lateral:** Acesso rápido aos colegas de equipa através de avatares circulares no topo da navegação, identificando a conversa ativa.
- **Prevenção de Conversas Duplicadas:** As conversas 1:1 garantem unicidade ao ordenar os identificadores dos dois participantes no momento da criação (`utilizador_um_id = min` e `utilizador_dois_id = max`).
- **Restrição de Integridade na Base de Dados:** Aplicação de restrição composta `UNIQUE` e restrição `CHECK (utilizador_um_id < utilizador_dois_id)` no motor MySQL 8.4, prevenindo duplicações ou conversas de um utilizador consigo próprio.
- **Controlo de Acesso às Conversas Privadas:** Acesso restrito aos dois intervenientes através da `ConversaDiretaPolicy`, gerando HTTP `403 Forbidden` para terceiros.

### 4. Envio Polimórfico de Mensagens (Salas e DMs)
- **Tabela Unificada `mensagens`:** Estrutura relacional com chaves polimórficas (`mensagem_tipo` e `mensagem_id`) e *morphMap* registado no `AppServiceProvider` (`'sala'` e `'conversa_direta'`).
- **Normalização de Conteúdo:** Aplicação de `trim` ao conteúdo textual antes da validação (`required|string|max:5000`), rejeitando mensagens compostas apenas por espaços em branco.
- **Ordenação Cronológica com Desempate:** Escopo local `scopeCronologico` ordenando as mensagens por ordem crescente de data (`created_at`) com desempate por identificador (`id`).
- **Formatação de Envio:** Apresentação temporal detalhada com dia e hora (`d/m/Y H:i`) e escape seguro anti-XSS (`nl2br(e(...))`).

### 5. Painel Administrativo de Colaboradores
- **Módulo CRUD Completo (`AdminUtilizadorController`):** Listagem paginada de colaboradores, formulário de registo e formulário de edição de perfis, permissões e estados.
- **Proteção contra Condições de Concorrência:** Operações de atualização e desativação executadas em transações com bloqueio pessimista (`lockForUpdate()`), protegendo contra a perda de administradores ativos.
- **Proteções de Segurança:**
  - Impedimento de um administrador desativar a própria conta em sessão;
  - Impedimento de um administrador revogar a sua própria permissão para utilizador comum;
  - Impedimento de desativação do último administrador ativo do sistema.
- **Modais de Confirmação DaisyUI:** Confirmação elegante em HTML/DaisyUI para a desativação de contas.

### 6. Interface Minimalista Estilo ONCE Campfire
- **Layout de Duas Colunas:** Área de conversação principal à esquerda com scroll independente e barra lateral direita unificada (`CampfireSidebar`) com Pings e cápsulas de salas.
- **Caixa de Escrita Flutuante em Cápsula:** Barra inferior arredondada (`rounded-full`) com input, submissão através da tecla `Enter` e ícone personalizado `send-svgrepo-com.svg`.
- **Avatares com Iniciais:** Suporte a fotografias de perfil carregadas e avatares por predefinição com iniciais em UTF-8 perfeitamente centradas.

---

## Demonstração Visual da Interface

* **Ecrã de Autenticação (Campfire Style):**
  ![Ecrã de Autenticação](Images%20Readme/Login.png)

* **Área de Chat de Grupo (Salas & Barra Lateral Direita):**
  ![Área de Chat de Grupo](Images%20Readme/Sala.png)

* **Módulo de Mensagens Diretas (Pings 1:1):**
  ![Mensagens Diretas](Images%20Readme/Mensagens.png)

* **Painel Administrativo de Colaboradores:**
  ![Painel Administrativo](Images%20Readme/Colaboradores.png)

---

## Padrões de Desenvolvimento
- **Zero Comentários no Código:** Código autoexplicativo gerido sem comentários de desenvolvimento (`//`, `/* */`).
- **Zero CSS Inline:** Utilização exclusiva de classes utilitárias do Tailwind CSS e DaisyUI v5.
- **Internacionalização (pt-PT):** Rótulos, mensagens de validação e respostas visíveis configurados estritamente em Português Europeu.

---

## Requisitos do Sistema
- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- Base de dados MySQL 8.4 (Laragon)

---

## Passos para Instalação

1. Clonar o repositório.
2. Criar previamente a base de dados **`inovcorp_chat`** no MySQL (via Laragon / phpMyAdmin / HeidiSQL / MySQL Workbench).
3. Copiar o ficheiro de configuração do ambiente:
   ```bash
   cp .env.example .env
   ```
4. Instalar as dependências PHP e JavaScript:
   ```bash
   composer install
   npm install
   ```
5. Configurar as credenciais da base de dados no ficheiro `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_DATABASE=inovcorp_chat
   DB_USERNAME=root
   DB_PASSWORD=
   ```
6. Gerar a chave de cifra da aplicação:
   ```bash
   php artisan key:generate
   ```
7. Limpar a cache de configuração:
   ```bash
   php artisan config:clear
   ``` 
8. Executar as migrações e o povoamento inicial (Seeder) — *Nota: o comando `migrate:fresh` elimina tabelas existentes e recria a base de dados, adequado para uma instalação limpa:*
   ```bash
   php artisan migrate:fresh --seed
   ```
9. Criar a ligação simbólica para o armazenamento público (avatares):
   ```bash
   php artisan storage:link
   ```
10. Compilar os recursos estáticos de frontend (Vite):
    ```bash
    npm run build
    ```
11. Iniciar o servidor de desenvolvimento local e aceder a `http://localhost:8000`:
    ```bash
    php artisan serve
    ```

---

## Testes Automatizados (PEST PHP)

O projeto dispõe de uma suite completa de testes funcionais e de integração em **PEST PHP**, cobrindo autenticação, recusa de contas inativas, regras RBAC, unicidade de conversas 1:1 e isolamento de mensagens privadas:

```bash
php artisan test
```
*(Última execução validada: 19 testes aprovados e 50 asserções concluídas com sucesso).*

---

## Projeto desenvolvido para
Este projeto foi desenvolvido por **Bruno Pinto** para a **INOVCORP**, no âmbito do estágio curricular.