# Hub de Integração ERP - Vesti

Este projeto é um Hub de Integração desenvolvido em **Laravel 12** e **PHP 8.3**. Seu objetivo é consumir produtos e variações de diferentes sistemas ERPs (como Xpto e Xyz), normalizar esses dados e sincronizá-los com a plataforma de vendas online Vesti através de APIs.

O sistema foi desenhado com foco em **escalabilidade**, **performance** e **reuso de código**, utilizando padrões de projeto (*Adapter*, *Factory* e *DTOs*) para suportar facilmente a entrada de **N novos ERPs** com estruturas diferentes, resultando na mesma saída, sem necessidade de modificar o núcleo da aplicação.

---

## 🚀 Tecnologias e Padrões Utilizados
- **PHP 8.3+** (Uso de `readonly classes` e `declare(strict_types=1)`)
- **Laravel 12**
- **Docker & Docker Compose** (Ambiente isolado com Nginx e PHP-FPM)
- **Design Patterns:**
  - *Adapter:* Para traduzir contratos específicos de cada ERP para o domínio da aplicação.
  - *Factory:* Para instanciar dinamicamente o ERP desejado sem ferir o princípio OCP do SOLID.
  - *DTO (Data Transfer Object):* Para garantir tipagem forte e imutabilidade dos dados trafegados.

---

## 🛠️ Instalação e Configuração (Docker)

Siga os passos abaixo para preparar o ambiente localmente:

1. Clone o repositório e acesse a pasta do projeto:
   ```bash
   git clone https://github.com/gustapng/teste-tecnico.git
   cd teste-tecnico
   ```

2. Suba a infraestrutura do Docker em background:
   ```Bash
   docker-compose up -d --build
   ```

3. Atualize e instale as dependências do Laravel:
   ```Bash
   docker-compose exec app composer update
   docker-compose exec app composer install
   ```

4. Configure o arquivo de variáveis de ambiente:
   ```Bash
   docker-compose exec app cp .env.example .env
   docker-compose exec app php artisan key:generate
   ```

5. Ajuste as permissões das pastas (Necessário para o SQLite de cache/sessão):
   ```
   Bash
   docker-compose exec app chmod -R 777 storage bootstrap/cache database
   ```

### A aplicação base estará rodando em: http://localhost:8000

---

## 🚀 Como executar os Testes Unitários
O projeto conta com testes unitários para garantir a integridade do Serviço de Sincronização. O teste utiliza o Http::fake() do Laravel e Mock Objects, o que significa que nenhuma requisição real é feita à Vesti durante os testes, preservando a integridade de dados externos.


Para rodar o teste, execute:
```Bash
docker-compose exec app php artisan test
```

---

## 🚀 Como executar uma Sincronização Real

Para testar o envio real dos dados formatados para a API da Vesti, é necessário preencher suas credenciais no arquivo .env.

Abra o arquivo .env gerado na raiz do projeto.

Preencha as variáveis de ambiente no final do arquivo:

**Variáveis:**

**VESTI_API_URL**=url_da_api_aqui<br>
**VESTI_API_TOKEN**=seu_token_aqui<br>
**VESTI_COMPANY_ID**=seu_id_da_empresa_aqui<br>

Execute o comando de sincronização via Artisan, informando qual ERP deseja ler (xpto ou xyz):

### Para sincronizar o ERP Xpto
```Bash
docker-compose exec app php artisan erp:sync xpto
```

### Para sincronizar o ERP Xyz
```Bash
docker-compose exec app php artisan erp:sync xyz
```

**Nota técnica: O sistema utiliza array_chunk para enviar lotes de 100 produtos por requisição, respeitando o limite e garantindo a performance da API.**

---

## 🔌 Como adicionar um novo ERP (Escalabilidade)
O projeto foi construído para que N estruturas diferentes possam ser acopladas sem alterar o núcleo da aplicação. Para adicionar um novo ERP (ex: Abc):

Crie o Adapter:
Crie uma classe **AbcAdapter** em **app/Adapters/** que estenda **AbstractAdapter** (ou implemente **ErpAdapterInterface**).

Mapeie os Dados:
Dentro do método **getProducts()**, leia a fonte de dados do ERP Abc e retorne um array de **App\DTOs\ProductDTO**. A lógica de normalização (como chaves de JSON diferentes) fica encapsulada apenas aqui.

Registre na Factory:
Abra a classe **app/Adapters/ErpAdapterFactory.php** e adicione a nova opção no bloco match:

Exemplo:

**'abc' => new AbcAdapter()**,

Pronto! O comando **php artisan erp:sync abc** já funcionará automaticamente e enviará os dados estruturados para a Vesti.

---

## 🤖 O uso da Inteligência Artificial a meu favor
A Inteligência Artificial foi utilizada neste projeto adotando a mentalidade de Pair Programming e Consultoria de Arquitetura, com o intuito de otimizar o tempo e validar estratégias técnicas:

Setup de Infraestrutura: Geração rápida do boilerplate do Docker (Dockerfile e docker-compose.yml) alinhado às exigências do PHP 8.3 e Laravel 12.

Troubleshooting: Resolução instantânea de configurações de permissão de diretórios (ex: SQLite em containers Docker).

Design de Software e Clean Code: A IA auxiliou no debate e na validação da arquitetura adotada. Discutimos a melhor forma de aplicar os padrões Adapter e Factory para solucionar o requisito de suportar N entradas diferentes com elegância. Também contribuiu na geração de Mocks para a construção dos testes unitários, assegurando a robustez da solução final.