-- ****************** RESILIENCIA QI ******************;
-- ***************************************************;
DROP TABLE %TABLE_PREFIX%resultados;


DROP TABLE %TABLE_PREFIX%preguntas;


DROP TABLE %TABLE_PREFIX%registros;
-- ************************************** %TABLE_PREFIX%preguntas

CREATE TABLE %TABLE_PREFIX%preguntas
(
 id       INT NOT NULL AUTO_INCREMENT,
 pregunta VARCHAR(500) NOT NULL ,
 tipo     CHAR NOT NULL ,
 grupo    VARCHAR(45) NOT NULL ,

PRIMARY KEY (id)
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%registros

CREATE TABLE %TABLE_PREFIX%registros
(
 id                INT NOT NULL AUTO_INCREMENT,
 nombre            VARCHAR(100) NOT NULL ,
 fechadenacimiento DATE NOT NULL ,
 edad              INT NOT NULL ,
 fechaaplicacion   DATETIME NOT NULL ,
 organizacion      VARCHAR(64) NOT NULL,

PRIMARY KEY (id)
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%resultados

CREATE TABLE %TABLE_PREFIX%resultados
(
 id           INT NOT NULL AUTO_INCREMENT,
 respuesta    CHAR NOT NULL ,
 pregunta     INT NOT NULL ,
 cuestionario INT NOT NULL ,

PRIMARY KEY (id),
KEY fkIdx_19 (pregunta),
CONSTRAINT FK_19 FOREIGN KEY fkIdx_19 (pregunta) REFERENCES %TABLE_PREFIX%preguntas (id) ON DELETE CASCADE ON UPDATE CASCADE,
KEY fkIdx_22 (cuestionario),
CONSTRAINT FK_22 FOREIGN KEY fkIdx_22 (cuestionario) REFERENCES %TABLE_PREFIX%registros (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;
