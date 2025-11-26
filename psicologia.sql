-- Tabla para almacenar los resultados del ICMM-III
CREATE TABLE IF NOT EXISTS mcmi_results (
    id SERIAL PRIMARY KEY,
    patient_id INTEGER REFERENCES mcmi_patients(id),
    evaluator_id INTEGER REFERENCES mcmi_users(id),
    evaluation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP,
    status VARCHAR(20) DEFAULT 'completed' CHECK (status IN ('in_progress', 'completed', 'archived')),
    
    -- Puntuaciones brutas
    raw_1 INTEGER, raw_2A INTEGER, raw_2B INTEGER, raw_3 INTEGER, raw_4 INTEGER, raw_5 INTEGER,
    raw_6A INTEGER, raw_6B INTEGER, raw_7 INTEGER, raw_8A INTEGER, raw_8B INTEGER,
    raw_S INTEGER, raw_C INTEGER, raw_P INTEGER,
    raw_A INTEGER, raw_H INTEGER, raw_N INTEGER, raw_D INTEGER, raw_B INTEGER, raw_T INTEGER, raw_R INTEGER,
    raw_SS INTEGER, raw_CC INTEGER, raw_PP INTEGER,
    raw_X INTEGER, raw_Y INTEGER, raw_Z INTEGER, raw_V INTEGER,
    
    -- Puntuaciones BR (Base Rate)
    br_1 INTEGER, br_2A INTEGER, br_2B INTEGER, br_3 INTEGER, br_4 INTEGER, br_5 INTEGER,
    br_6A INTEGER, br_6B INTEGER, br_7 INTEGER, br_8A INTEGER, br_8B INTEGER,
    br_S INTEGER, br_C INTEGER, br_P INTEGER,
    br_A INTEGER, br_H INTEGER, br_N INTEGER, br_D INTEGER, br_B INTEGER, br_T INTEGER, br_R INTEGER,
    br_SS INTEGER, br_CC INTEGER, br_PP INTEGER,
    br_X INTEGER, br_Y INTEGER, br_Z INTEGER, br_V INTEGER,
    
    -- Respuestas individuales (JSON para flexibilidad)
    responses JSONB,
    
    -- Interpretación y observaciones
    primary_interpretation TEXT,
    clinical_notes TEXT,
    recommendations TEXT,
    
    -- Metadatos
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para mejorar el rendimiento de las consultas
CREATE INDEX IF NOT EXISTS idx_mcmi_results_patient_id ON mcmi_results(patient_id);
CREATE INDEX IF NOT EXISTS idx_mcmi_results_evaluator_id ON mcmi_results(evaluator_id);
CREATE INDEX IF NOT EXISTS idx_mcmi_results_evaluation_date ON mcmi_results(evaluation_date);
CREATE INDEX IF NOT EXISTS idx_mcmi_results_status ON mcmi_results(status);

-- Trigger para actualizar updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_mcmi_results_updated_at 
    BEFORE UPDATE ON mcmi_results 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();