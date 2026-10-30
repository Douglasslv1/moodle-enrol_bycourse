<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Conditional self-enrolment by course plugin.
 *
 * @package    enrol_bycourse
 * @copyright  2026 Douglas Silva <douglas.aarq95@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Autoinscrição condicional por curso';
$string['pluginname_desc'] = 'Permite a autoinscrição somente quando o usuário possui matrículas ativas nos cursos de requisito selecionados.';
$string['bycourse:config'] = 'Configurar autoinscrição condicional';
$string['bycourse:enrolself'] = 'Fazer autoinscrição quando os requisitos forem atendidos';
$string['bycourse:manage'] = 'Gerenciar autoinscrições condicionais';
$string['bycourse:unenrol'] = 'Desinscrever usuários inscritos por este método';
$string['requiredcourses'] = 'Cursos de requisito';
$string['requiredcourses_help'] = 'Selecione os cursos nos quais o usuário precisa possuir matrícula atual, ativa e não suspensa.';
$string['requiredcourseserror'] = 'Selecione pelo menos um curso de requisito.';
$string['invalidrequiredcourse'] = 'Um ou mais cursos de requisito selecionados não existem mais.';
$string['targetcoursenotallowed'] = 'O curso de destino não pode ser requisito dele mesmo.';
$string['matchmode'] = 'Forma de atendimento dos requisitos';
$string['matchany'] = 'Qualquer curso selecionado';
$string['matchall'] = 'Todos os cursos selecionados';
$string['instructions'] = 'Instruções para inscrição';
$string['instructions_help'] = 'Texto mostrado ao usuário na página de inscrição. Use-o para explicar os requisitos e como atendê-los.';
$string['role'] = 'Papel atribuído';
$string['defaultrole_desc'] = 'Papel atribuído quando o usuário se inscreve por este método.';
$string['maxenrolled'] = 'Máximo de usuários inscritos';
$string['maxenrolled_help'] = 'Número máximo de matrículas criadas por esta instância. Zero significa ilimitado.';
$string['allownewenrolments'] = 'Permitir novas inscrições';
$string['enrolme'] = 'Inscrever-me';
$string['eligible'] = 'Você atende aos requisitos de inscrição.';
$string['noteligible'] = 'Você não pode se inscrever porque não possui matrícula ativa no(s) curso(s) exigido(s).';
$string['existinginactiveenrolment'] = 'Você já possui uma matrícula inativa por este método. Entre em contato com a administração do curso.';
$string['coursecardicon'] = 'Autoinscrição condicional disponível';
$string['enrolmentclosed'] = 'Novas inscrições estão fechadas no momento.';
$string['enrolmentnotstarted'] = 'As inscrições serão abertas em {$a}.';
$string['enrolmentended'] = 'As inscrições foram encerradas em {$a}.';
$string['maxenrolledreached'] = 'O limite de inscrições foi atingido.';
$string['privacy:metadata'] = 'O plugin de autoinscrição condicional não armazena dados pessoais adicionais.';

