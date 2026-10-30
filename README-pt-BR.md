# Autoinscrição condicional por curso (`enrol_bycourse`)

Método de inscrição para Moodle que permite a autoinscrição somente quando o
usuário possui matrícula ativa nos cursos definidos como requisito.

## Funcionamento

O administrador seleciona um ou mais cursos e escolhe uma regra:

- **Qualquer curso:** uma matrícula ativa em pelo menos um curso atende ao requisito.
- **Todos os cursos:** é necessária matrícula ativa em todos os cursos selecionados.

Matrículas suspensas, expiradas ou ainda não iniciadas não atendem à condição.
Um curso concluído continua válido somente enquanto a matrícula permanecer ativa.
O plugin nunca modifica as matrículas dos cursos de requisito.

## Recursos

- múltiplos cursos de requisito;
- instruções personalizadas;
- ícone nas listagens de cursos suportadas pelo tema;
- datas de disponibilidade, duração e limite de vagas;
- papel atribuído e mensagem de boas-vindas;
- português do Brasil e inglês;
- backup e restauração segura da configuração.

## Instalação

1. Instale o ZIP ou coloque a pasta em `enrol/bycourse`.
2. Acesse **Administração do site > Notificações**.
3. No curso de destino, abra **Participantes > Métodos de inscrição**.
4. Adicione **Autoinscrição condicional por curso** e configure os requisitos.

## Manutenção e suporte

Douglas Silva — <douglas.aarq95@gmail.com>

## Licença

GNU GPL v3 ou posterior. O plugin não utiliza chave de ativação, servidor de
licenciamento, telemetria ou dependência externa para funcionar.

