# Mega-Sena - Sistema de Apostas

## Descrição
Este é um sistema de apostas para a Mega-Sena, desenvolvido em PHP. A aplicação permite que usuários façam suas apostas, gerem números aleatórios para o sorteio e comparem seus acertos com os números sorteados. Cada aposta realizada é salva em um arquivo TXT individual e pode ser visualizada diretamente na interface da aplicação.

## Funcionalidades
- **Login e Sessão**: O sistema requer autenticação para acesso.
- **Cadastro de Apostas**: O usuário pode escolher 6 números entre 1 e 60.
- **Sorteio Automático**: O sistema gera aleatoriamente os números sorteados.
- **Armazenamento de Apostas**: Cada aposta realizada é salva em um arquivo TXT individual na pasta `apostas/`.
- **Histórico de Apostas**: Exibe todas as apostas feitas pelo usuário na interface.
- **Exclusão de Apostas**: O usuário pode excluir apostas específicas diretamente pela interface.
- **Download do Histórico**: Permite o download do histórico de apostas.

## Requisitos
- Servidor Apache ou ambiente de desenvolvimento com suporte a PHP.
- PHP 7.4 ou superior.
- Pasta `apostas/` com permissão de escrita.

## Como Usar
1. **Instale e configure o ambiente** com um servidor Apache e suporte a PHP.
2. **Acesse a página inicial (`index.php`)** e faça login.
3. **Realize uma aposta** escolhendo 6 números e enviando o formulário.
4. **Veja o resultado** comparando seus números com os números sorteados.
5. **Acompanhe o histórico** de apostas, visualize ou exclua apostas conforme necessário.

## Estrutura do Projeto
- `index.php` - Página principal contendo o formulário de apostas e exibição do histórico.
- `apostas/` - Diretório onde as apostas são salvas individualmente em arquivos `.txt`.
- `img/` - Pasta contendo a imagem ilustrativa da Mega-Sena.
- `login.php` - Página de login do sistema.

## Observações
- Cada aposta salva no diretório `apostas/` possui um identificador único.
- Caso a pasta `apostas/` não exista, certifique-se de criá-la e conceder permissões adequadas.
- O sistema pode ser adaptado para armazenar dados em um banco de dados caso necessário.
