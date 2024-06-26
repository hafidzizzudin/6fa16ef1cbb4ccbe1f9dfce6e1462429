CREATE TYPE status AS ENUM ('pending', 'success', 'failed');

CREATE TABLE IF NOT EXISTS email (
    id BIGSERIAL PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL DEFAULT '',
    is_html BOOLEAN NOT NULL DEFAULT FALSE,
    email_to VARCHAR(255) NOT NULL DEFAULT '',
    body TEXT,
    subject VARCHAR(255) NOT NULL DEFAULT '',
    status status DEFAULT 'pending',
    note TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);

CREATE INDEX IF NOT EXISTS IDX_user_id ON email (user_id);

CREATE INDEX IF NOT EXISTS IDX_email_to ON email (email_to);