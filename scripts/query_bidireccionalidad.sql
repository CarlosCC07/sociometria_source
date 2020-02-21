UPDATE personas
SET bidr = 1
WHERE `idTrabajador` IN (
	SELECT e.idTrabajador FROM encuestaPersona e
WHERE e.idTrabajador IN 
	(SELECT `idAscendencia1` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idAscendencia2` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idAscendencia3` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idAfinidad1` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idAfinidad2` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idAfinidad3` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idPopularidad1` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idPopularidad2` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1) OR
    e.idTrabajador IN (SELECT `idPopularidad3` FROM encuestaPersona b WHERE b.idTrabajador = e.idAscendencia1 OR b.idTrabajador = e.idAscendencia2 OR b.idTrabajador = e.idAscendencia3 OR b.idTrabajador = e.idAfinidad1 OR b.idTrabajador = e.idAfinidad2 OR b.idTrabajador = e.idAfinidad3 OR b.idTrabajador = e.idPopularidad1 OR b.idTrabajador = e.idPopularidad2 OR b.idTrabajador = e.idPopularidad1)
		)