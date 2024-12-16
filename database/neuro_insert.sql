/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 8.0.37 : Database - prasca
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`prasca` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `prasca`;

/*Data for the table `antecedentes_familiares_neuro` */

insert  into `antecedentes_familiares_neuro`(`id`,`id_historia`,`tipo`,`detalle`) values 
(1,1,'depresion','padre'),
(2,1,'ansiedad','padre'),
(3,1,'demencia','madre'),
(4,1,'alcoholismo','madre'),
(5,1,'drogadiccion','madre'),
(6,1,'discapacidad_intelectual','madre'),
(7,1,'patologicos','madre'),
(8,1,'otros','padre'),
(9,2,'depresion','no_refiere'),
(10,2,'ansiedad','primo'),
(11,2,'demencia','tio'),
(12,2,'alcoholismo','abuelo'),
(13,2,'drogadiccion','primo'),
(14,2,'discapacidad_intelectual','primo'),
(15,2,'patologicos','primo'),
(16,2,'otros','primo'),
(17,3,'depresion','padre'),
(18,3,'ansiedad','madre'),
(19,3,'demencia','hijo'),
(20,3,'alcoholismo','hijo'),
(21,3,'drogadiccion','sobrino'),
(22,3,'discapacidad_intelectual','primo'),
(23,3,'patologicos','primo'),
(24,3,'otros','primo');

/*Data for the table `antecedentes_medicos_neuro` */

insert  into `antecedentes_medicos_neuro`(`id`,`id_historia`,`tipo`,`detalle`) values 
(1,1,'quirurgicos','no quirur'),
(2,1,'toxicos','si tiene'),
(3,1,'traumaticos','luxaciones_esguinces'),
(4,1,'medicacion','<p>un dolex</p>'),
(5,1,'paraclinicos','paraclinico'),
(6,1,'hospitalizaciones','por fractura'),
(7,1,'patologia','no tienen'),
(8,2,'quirurgicos','si tiene'),
(9,2,'toxicos','no'),
(10,2,'traumaticos','luxaciones_esguinces'),
(11,2,'medicacion','<p>un acetaminofen</p>'),
(12,2,'paraclinicos','no'),
(13,2,'hospitalizaciones','por tos'),
(14,2,'patologia','ejemplo'),
(15,3,'quirurgicos','ttt'),
(16,3,'toxicos','ppp'),
(17,3,'traumaticos','luxaciones_esguinces'),
(18,3,'medicacion','<p>un diclofenaco y acetaminofen</p>'),
(19,3,'paraclinicos','prueba'),
(20,3,'hospitalizaciones','no tiene, no hay'),
(21,3,'patologia','se le olvida todo');

/*Data for the table `apariencia_personal_neuro` */

insert  into `apariencia_personal_neuro`(`id`,`id_historia`,`caracteristica`,`detalle`) values 
(1,1,'edad','1'),
(2,1,'aseo','7'),
(3,1,'salud','13'),
(4,1,'facies','15'),
(5,1,'biotipo','26'),
(6,1,'actitud','208'),
(7,2,'edad','1'),
(8,2,'aseo','7'),
(9,2,'salud','13'),
(10,2,'facies','15'),
(11,2,'biotipo','26'),
(12,2,'actitud','214'),
(13,3,'edad','1'),
(14,3,'aseo','7'),
(15,3,'salud','13'),
(16,3,'facies','15'),
(17,3,'biotipo','26'),
(18,3,'actitud','213');

/*Data for the table `funciones_cognitivas_neuro` */

insert  into `funciones_cognitivas_neuro`(`id`,`id_historia`,`caracteristica`,`detalle`) values 
(74,1,'consciencia','34'),
(75,1,'orientacion','37'),
(76,1,'memoria','40'),
(77,1,'atencion','43'),
(78,1,'concentracion','48'),
(79,1,'lenguaje','53'),
(80,1,'pensamiento','66'),
(81,1,'afecto','101'),
(82,1,'sensopercepcion','110'),
(83,1,'psicomotricidad','118'),
(84,1,'juicio','130'),
(85,1,'inteligencia','133'),
(86,1,'conciencia_enfermedad','138'),
(87,1,'sufrimiento_psicologico','141'),
(88,1,'motivacion_tratamiento','201'),
(89,2,'consciencia','34'),
(90,2,'orientacion','37'),
(91,2,'memoria','40'),
(92,2,'atencion','43'),
(93,2,'concentracion','48'),
(94,2,'lenguaje','53'),
(95,2,'pensamiento','66'),
(96,2,'afecto','101'),
(97,2,'sensopercepcion','110'),
(98,2,'psicomotricidad','118'),
(99,2,'juicio','130'),
(100,2,'inteligencia','133'),
(101,2,'conciencia_enfermedad','138'),
(102,2,'sufrimiento_psicologico','141'),
(103,2,'motivacion_tratamiento','201'),
(104,3,'consciencia','35'),
(105,3,'orientacion','37'),
(106,3,'memoria','40'),
(107,3,'atencion','43'),
(108,3,'concentracion','48'),
(109,3,'lenguaje','53'),
(110,3,'pensamiento','66'),
(111,3,'afecto','101'),
(112,3,'sensopercepcion','110'),
(113,3,'psicomotricidad','118'),
(114,3,'juicio','130'),
(115,3,'inteligencia','133'),
(116,3,'conciencia_enfermedad','138'),
(117,3,'sufrimiento_psicologico','141'),
(118,3,'motivacion_tratamiento','202');

/*Data for the table `funciones_somaticas_neuro` */

insert  into `funciones_somaticas_neuro`(`id`,`id_historia`,`ciclos_del_sueno`,`apetito`,`actividades_autocuidado`) values 
(1,1,'8888','7777','6666'),
(2,2,'bbbb','vvvv','xxxxx'),
(3,3,'2 horas al dia','24 siempre','no tiene');

/*Data for the table `historia_ajuste_desempeno_neuro` */

insert  into `historia_ajuste_desempeno_neuro`(`id`,`id_historia`,`area`,`detalle`) values 
(1,1,'historia_educativa','1111'),
(2,1,'historia_laboral','2222'),
(3,1,'historia_familiar','3333'),
(4,1,'historia_social','4444'),
(5,1,'historia_socio_afectiva','5555'),
(6,2,'historia_educativa','hhhh'),
(7,2,'historia_laboral','gggg'),
(8,2,'historia_familiar','ffff'),
(9,2,'historia_social','dddd'),
(10,2,'historia_socio_afectiva','ssss'),
(11,3,'historia_educativa','mmmm'),
(12,3,'historia_laboral','nnnn'),
(13,3,'historia_familiar','bbbb'),
(14,3,'historia_social','vvvv'),
(15,3,'historia_socio_afectiva','cccc');

/*Data for the table `historia_clinica_neuro` */

insert  into `historia_clinica_neuro`(`id`,`id_paciente`,`id_profesional`,`remision`,`dx_principal`,`motivo_consulta`,`otro_motivo_consulta`,`enfermedad_actual`,`codigo_consulta`,`codigo_diagnostico`,`diagnostico_primera_vez`,`objetivo_general`,`objetivos_especificos`,`sugerencias_interconsultas`,`observaciones_recomendaciones`,`fecha_historia`,`estado_hitoria`,`estado_registro`,`notas_importantes`,`tipologia`,`plan_intervension`) values 
(1,495,1682,'<p>sin remision</p>',4,'219',NULL,'<p>no tiene enfermedad</p>','31','14','si','<p>uuuu</p>','<p>yyyy</p>','<p>tttt</p>','<p>rrrr</p>','2024-12-15 13:09:24','abierta','ACTIVO',NULL,'Adulto','173'),
(2,492,1682,'<p>con remision</p>',23,'218',NULL,'<p>gripe</p>','10054','6','no','<p>llll</p>','<p>pppp</p>','<p>yyyyy</p>','<p>zzzzzz</p>','2024-12-15 13:13:42','abierta','ACTIVO',NULL,'Adulto','172'),
(3,496,1682,'<p>remision desde eps salud total</p>',26,'220',NULL,'<p>enfermedad mental</p>','105','7064','si','<p>yyyy</p>','<p>ffff</p>','<p>gggg hhhh</p>','<p>uuuu iiii</p>','2024-12-15 14:45:16','abierta','ACTIVO',NULL,'Adulto','174');

/*Data for the table `interconsultas_neuro` */

insert  into `interconsultas_neuro`(`id`,`id_historia`,`tipo`,`detalle`) values 
(1,1,'intervencion_psiquiatria','6666'),
(2,1,'intervencion_neurologia','7777'),
(3,1,'intervencion_neuropsicologia','8888'),
(4,2,'intervencion_psiquiatria','aaaa'),
(5,2,'intervencion_neurologia','1111'),
(6,2,'intervencion_neuropsicologia','qqqq'),
(7,3,'intervencion_psiquiatria','meter al psiquiátrico'),
(8,3,'intervencion_neurologia','sedarlo'),
(9,3,'intervencion_neuropsicologia','sedarlo fuerte');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
