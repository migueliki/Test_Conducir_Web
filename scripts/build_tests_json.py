#!/usr/bin/env python3
"""
Script SIMPLE para generar los JSONs de tests normales y señales.
Sin complejidad: extrae texto, lo divide por preguntas (líneas que empiezan por número),
y agrupa las opciones.
"""

import re
from pathlib import Path
import zipfile
import xml.etree.ElementTree as ET

WORKSPACE = Path(__file__).resolve().parent.parent
NORMALES_DIR = WORKSPACE / 'Test_Conducir_Preguntas' / 'TEST_NORMALES'
SENALES_DIR = WORKSPACE / 'Test_Conducir_Preguntas' / 'TEST_SEÑALES'
DATA_DIR = WORKSPACE / 'data'

def extract_text_from_docx(docx_path):
    """Extrae el texto de un DOCX línea por línea."""
    text_lines = []
    try:
        with zipfile.ZipFile(docx_path, 'r') as zip_ref:
            xml_content = zip_ref.read('word/document.xml').decode('utf-8')
            # Separar por </w:t> (fin de cada segmento de texto)
            parts = xml_content.split('</w:t>')
            for part in parts:
                # Extraer el contenido después de <w:t...>
                match = re.search(r'<w:t[^>]*>(.*?)$', part, re.DOTALL)
                if match:
                    text = match.group(1).strip()
                    if text:
                        text_lines.append(text)
    except Exception as e:
        print(f"Error leyendo {docx_path}: {e}")
    return text_lines

def parse_preguntas_from_lines(lines):
    """Convierte líneas de texto en estructura de preguntas."""
    preguntas = []
    i = 0
    while i < len(lines):
        line = lines[i].strip()
        
        # Detectar inicio de pregunta (número seguido de punto)
        match = re.match(r'^(\d+)\.$', line)
        if match:
            # Siguiente línea es la pregunta
            i += 1
            if i >= len(lines):
                break
            pregunta_text = lines[i].strip()
            
            # Recopilar las 3-4 opciones (líneas que no comienzan con número)
            opciones = []
            i += 1
            while i < len(lines) and len(opciones) < 4:
                opt_line = lines[i].strip()
                
                # Si es un número seguido de punto, es una nueva pregunta - parar
                if re.match(r'^\d+\.$', opt_line):
                    i -= 1  # Retroceder para que la siguiente iteración procese esta pregunta
                    break
                
                # Si la línea no está vacía y no es un número, es una opción
                if opt_line and not re.match(r'^\d+\.$', opt_line):
                    opciones.append(opt_line)
                
                i += 1
            
            # Crear la pregunta si hay al menos pregunta y 3 opciones
            if pregunta_text and len(opciones) >= 3:
                preguntas.append({
                    "pregunta": pregunta_text,
                    "imagen": "",
                    "opciones": opciones[:4],  # Máx 4 opciones
                    "respuesta_correcta": 0  # Por defecto
                })
        else:
            i += 1
    
    return preguntas

def process_folder(folder_path, output_key_prefix, json_filename):
    """Procesa una carpeta de tests."""
    all_tests = {}
    
    for docx_file in sorted(folder_path.glob('*.docx')):
        print(f"Procesando {docx_file.name}...")
        
        lines = extract_text_from_docx(docx_file)
        preguntas = parse_preguntas_from_lines(lines)
        
        # Nombre del test basado en el archivo
        test_name = docx_file.stem.replace('.', '_').lower()
        
        all_tests[test_name] = {
            "nombre": docx_file.stem,
            "preguntas": preguntas
        }
    
    # Escribir JSON
    import json
    json_path = DATA_DIR / json_filename
    with open(json_path, 'w', encoding='utf-8') as f:
        json.dump(all_tests, f, ensure_ascii=False, indent=2)
    
    print(f"✓ Guardado: {json_path}")
    print(f"  Total tests: {len(all_tests)}")
    return all_tests

# Procesar tests normales
print("=" * 60)
print("PROCESANDO TESTS NORMALES")
print("=" * 60)
normales = process_folder(NORMALES_DIR, 'test_', 'tests_normales.json')

print("\n" + "=" * 60)
print("PROCESANDO TESTS SEÑALES")
print("=" * 60)
senales = process_folder(SENALES_DIR, 'senal_', 'tests_senales.json')

print("\n✓ COMPLETADO")
