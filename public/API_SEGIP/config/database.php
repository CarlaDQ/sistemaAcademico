<?php
define('DB_PATH', __DIR__ . '/../database/rude.db');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec("PRAGMA journal_mode=WAL");
        $pdo->exec("PRAGMA foreign_keys=ON");
    }
    return $pdo;
}

function initDB(): void {
    $db = getDB();
    $db->exec("
        CREATE TABLE IF NOT EXISTS estudiantes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            codigo_rude TEXT UNIQUE NOT NULL,
            ci TEXT,
            nombre TEXT NOT NULL,
            apellido TEXT NOT NULL,
            fecha_nacimiento TEXT,
            lugar_nacimiento TEXT,
            nacionalidad TEXT DEFAULT 'Boliviana',
            idioma_materno TEXT DEFAULT 'Castellano',
            discapacidad INTEGER DEFAULT 0,
            discapacidad_tipo TEXT,
            departamento TEXT,
            provincia TEXT,
            localidad TEXT,
            zona TEXT,
            direccion TEXT,
            telefono TEXT,
            tutor_ci TEXT,
            tutor_nombre TEXT,
            curso TEXT,
            unidad_educativa TEXT,
            gestion INTEGER,
            estado TEXT DEFAULT 'activo',
            fecha_registro TEXT DEFAULT (datetime('now','-4 hours')),
            fecha_actualizacion TEXT DEFAULT (datetime('now','-4 hours'))
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS sincronizacion_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo TEXT,
            codigos_rude TEXT,
            total INTEGER,
            exitosos INTEGER,
            fallidos INTEGER,
            fecha TEXT DEFAULT (datetime('now','-4 hours'))
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS segip_personas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ci TEXT UNIQUE NOT NULL,
            ci_complemento TEXT,
            nombre TEXT NOT NULL,
            apellido_paterno TEXT NOT NULL,
            apellido_materno TEXT,
            fecha_nacimiento TEXT,
            lugar_nacimiento TEXT,
            departamento TEXT,
            provincia TEXT,
            localidad TEXT,
            genero TEXT,
            estado_civil TEXT,
            profesion TEXT,
            domicilio TEXT,
            fecha_emision TEXT,
            fecha_vencimiento TEXT,
            fecha_registro TEXT DEFAULT (datetime('now','-4 hours'))
        )
    ");
    $db->exec("
        CREATE TABLE IF NOT EXISTS integracion_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo_consulta TEXT,
            ci TEXT,
            codigo_rude_generado TEXT,
            datos_enviados TEXT,
            respuesta_segip TEXT,
            fecha TEXT DEFAULT (datetime('now','-4 hours'))
        )
    ");
}

function jsonResponse(mixed $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    jsonResponse(['success' => false, 'error' => $message], $code);
}

function input(): array {
    $json = file_get_contents('php://input');
    return json_decode($json, true) ?? [];
}
