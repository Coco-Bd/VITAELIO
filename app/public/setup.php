<?php
$db = new SQLite3('users.db');

// Create the users table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
)");

// Create the cv_data table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS cv_data (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    title TEXT,
     description TEXT,
    name TEXT,
    email TEXT,
    phone TEXT,
    address TEXT,
    education TEXT,
    experience TEXT,
    skills TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

echo "Database initialized!";