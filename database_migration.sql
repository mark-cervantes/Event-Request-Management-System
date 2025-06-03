-- Migration to add image_data column for base64 storage
-- Run this SQL script to modify the business_certificate table

USE sta_cruz_db;

-- Add new column for storing base64 encoded image data
ALTER TABLE business_certificate 
ADD COLUMN image_data LONGTEXT NULL COMMENT 'Base64 encoded image data' 
AFTER path;

-- The 'path' column will be kept for backward compatibility and can be removed later
-- For now, both columns will exist during the transition
