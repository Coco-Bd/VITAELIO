<?php
$db = new SQLite3('users.db');

// Create the users table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_firstName TEXT,
    user_lastName TEXT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user'
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

// Create the projects table if it doesn't exist
$db->exec("CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    technologies TEXT,
    image TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

echo "Database initialized!";