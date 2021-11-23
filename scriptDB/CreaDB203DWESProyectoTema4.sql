/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  daw2
 * Created: 4 nov. 2021
 */

create database DB203DWESProyectoTema4;
use DB203DWESProyectoTema4;

create user 'User203DWESProyectoTema4'@'%' IDENTIFIED BY 'P@ssw0rd';
grant all privileges on DB203DWESProyectoTema4.* to 'User203DWESProyectoTema4'@'%';

CREATE TABLE IF NOT EXISTS Departamento(
    CodDepartamento varchar(3) PRIMARY KEY,
    DescDepartamento varchar(255) NOT NULL,
    FechaBaja date NULL,
    VolumenNegocio float NULL
)engine=innodb;


