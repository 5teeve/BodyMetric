-- Add objectif column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN objectif VARCHAR(50) DEFAULT NULL AFTER is_gold;
