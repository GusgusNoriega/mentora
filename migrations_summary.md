# Resumen de Migraciones del Proyecto Mentora

Este archivo contiene un resumen de todas las migraciones de la base de datos, incluyendo campos y relaciones.

## Tabla: users

**Campos:**
- id: id (primary)
- name: string
- email: string (unique)
- email_verified_at: timestamp (nullable)
- password: string
- remember_token: rememberToken
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: password_reset_tokens

**Campos:**
- email: string (primary)
- token: string
- created_at: timestamp (nullable)

**Relaciones:**
- Ninguna

## Tabla: sessions

**Campos:**
- id: string (primary)
- user_id: foreignId (nullable, index)
- ip_address: string(45) (nullable)
- user_agent: text (nullable)
- payload: longText
- last_activity: integer (index)

**Relaciones:**
- user_id -> users.id

## Tabla: cache

**Campos:**
- key: string (primary)
- value: mediumText
- expiration: integer

**Relaciones:**
- Ninguna

## Tabla: cache_locks

**Campos:**
- key: string (primary)
- owner: string
- expiration: integer

**Relaciones:**
- Ninguna

## Tabla: jobs

**Campos:**
- id: id (primary)
- queue: string (index)
- payload: longText
- attempts: unsignedTinyInteger
- reserved_at: unsignedInteger (nullable)
- available_at: unsignedInteger
- created_at: unsignedInteger

**Relaciones:**
- Ninguna

## Tabla: job_batches

**Campos:**
- id: string (primary)
- name: string
- total_jobs: integer
- pending_jobs: integer
- failed_jobs: integer
- failed_job_ids: longText
- options: mediumText (nullable)
- cancelled_at: integer (nullable)
- created_at: integer
- finished_at: integer (nullable)

**Relaciones:**
- Ninguna

## Tabla: failed_jobs

**Campos:**
- id: id (primary)
- uuid: string (unique)
- connection: text
- queue: text
- payload: longText
- exception: longText
- failed_at: timestamp (useCurrent)

**Relaciones:**
- Ninguna

## Tabla: permissions

**Campos:**
- id: bigIncrements (primary)
- name: string
- guard_name: string
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: roles

**Campos:**
- id: bigIncrements (primary)
- team_foreign_key: unsignedBigInteger (nullable, index) (si teams)
- name: string
- guard_name: string
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: model_has_permissions

**Campos:**
- permission_id: unsignedBigInteger
- model_type: string
- model_morph_key: unsignedBigInteger (index)
- team_foreign_key: unsignedBigInteger (nullable, index) (si teams)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- permission_id -> permissions.id (cascade)
- team_foreign_key -> teams.id (si teams)

## Tabla: model_has_roles

**Campos:**
- role_id: unsignedBigInteger
- model_type: string
- model_morph_key: unsignedBigInteger (index)
- team_foreign_key: unsignedBigInteger (nullable, index) (si teams)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- role_id -> roles.id (cascade)
- team_foreign_key -> teams.id (si teams)

## Tabla: role_has_permissions

**Campos:**
- permission_id: unsignedBigInteger
- role_id: unsignedBigInteger

**Relaciones:**
- permission_id -> permissions.id (cascade)
- role_id -> roles.id (cascade)

## Tabla: oauth_auth_codes

**Campos:**
- id: char(80) (primary)
- user_id: foreignId (index)
- client_id: foreignUuid
- scopes: text (nullable)
- revoked: boolean
- expires_at: dateTime (nullable)

**Relaciones:**
- user_id -> users.id
- client_id -> oauth_clients.id

## Tabla: oauth_access_tokens

**Campos:**
- id: char(80) (primary)
- user_id: foreignId (nullable, index)
- client_id: foreignUuid
- name: string (nullable)
- scopes: text (nullable)
- revoked: boolean
- created_at: timestamp
- updated_at: timestamp
- expires_at: dateTime (nullable)

**Relaciones:**
- user_id -> users.id
- client_id -> oauth_clients.id

## Tabla: oauth_refresh_tokens

**Campos:**
- id: char(80) (primary)
- access_token_id: char(80) (index)
- revoked: boolean
- expires_at: dateTime (nullable)

**Relaciones:**
- access_token_id -> oauth_access_tokens.id

## Tabla: oauth_clients

**Campos:**
- id: uuid (primary)
- owner_type: string (nullable) (morphs)
- owner_id: unsignedBigInteger (nullable) (morphs)
- name: string
- secret: string (nullable)
- provider: string (nullable)
- redirect_uris: text
- grant_types: text
- revoked: boolean
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- owner -> morphs (users, etc.)

## Tabla: oauth_device_codes

**Campos:**
- id: char(80) (primary)
- user_id: foreignId (nullable, index)
- client_id: foreignUuid (index)
- user_code: char(8) (unique)
- scopes: text
- revoked: boolean
- user_approved_at: dateTime (nullable)
- last_polled_at: dateTime (nullable)
- expires_at: dateTime (nullable)

**Relaciones:**
- user_id -> users.id
- client_id -> oauth_clients.id

## Tabla: courses

**Campos:**
- id: id (primary)
- title: string
- slug: string (unique)
- summary: text (nullable)
- description: text (nullable)
- thumbnail_url: string (nullable)
- level: string (nullable)
- language: string (nullable)
- status: string (default 'draft')
- access_mode: string (default 'free')
- price_cents: integer (default 0)
- currency: char(3) (default 'USD')
- created_by: unsignedBigInteger
- published_at: timestamp (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- created_by -> users.id (cascade)

## Tabla: course_sections

**Campos:**
- id: id (primary)
- course_id: unsignedBigInteger
- title: string
- position: integer (default 0)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- course_id -> courses.id (cascade)

## Tabla: course_lessons

**Campos:**
- id: id (primary)
- section_id: unsignedBigInteger
- title: string
- content_type: string
- content_url: string (nullable)
- duration_seconds: integer (nullable)
- is_preview: boolean (default false)
- position: integer (default 0)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- section_id -> course_sections.id (cascade)

## Tabla: course_instructors

**Campos:**
- course_id: unsignedBigInteger
- user_id: unsignedBigInteger
- is_primary: boolean (default false)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- course_id -> courses.id (cascade)
- user_id -> users.id (cascade)

## Tabla: categories

**Campos:**
- id: id (primary)
- name: string
- slug: string (unique)
- parent_id: unsignedBigInteger (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- parent_id -> categories.id (set null)

## Tabla: course_category

**Campos:**
- course_id: unsignedBigInteger
- category_id: unsignedBigInteger
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- course_id -> courses.id (cascade)
- category_id -> categories.id (cascade)

## Tabla: tags

**Campos:**
- id: id (primary)
- name: string
- slug: string (unique)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: course_tag

**Campos:**
- course_id: unsignedBigInteger
- tag_id: unsignedBigInteger
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- course_id -> courses.id (cascade)
- tag_id -> tags.id (cascade)

## Tabla: enrollments

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- course_id: unsignedBigInteger
- source: string (nullable)
- enrolled_at: timestamp
- expires_at: timestamp (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- user_id -> users.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: lesson_progress

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- lesson_id: unsignedBigInteger
- progress_pct: decimal(5,2) (default 0)
- seconds_watched: integer (default 0)
- completed_at: timestamp (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- user_id -> users.id (cascade)
- lesson_id -> course_lessons.id (cascade)

## Tabla: course_progress

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- course_id: unsignedBigInteger
- progress_pct: decimal(5,2) (default 0)
- completed_at: timestamp (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- user_id -> users.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: subscription_plans

**Campos:**
- id: id (primary)
- name: string
- description: text (nullable)
- price_cents: integer
- currency: char(3) (default 'USD')
- interval: string (default 'monthly')
- trial_days: integer (default 0)
- access_all_courses: boolean (default false)
- is_active: boolean (default true)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: plan_course

**Campos:**
- plan_id: unsignedBigInteger
- course_id: unsignedBigInteger
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- plan_id -> subscription_plans.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: user_subscriptions

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- plan_id: unsignedBigInteger
- status: string
- started_at: timestamp (nullable)
- current_period_start: timestamp (nullable)
- current_period_end: timestamp (nullable)
- canceled_at: timestamp (nullable)
- ends_at: timestamp (nullable)
- provider: string (nullable)
- provider_sub_id: string (nullable)
- last_payment_at: timestamp (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- user_id -> users.id (cascade)
- plan_id -> subscription_plans.id (cascade)

## Tabla: payment_transactions

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- subscription_id: unsignedBigInteger (nullable)
- course_id: unsignedBigInteger (nullable)
- amount_cents: integer
- currency: char(3) (default 'USD')
- status: string
- provider: string (nullable)
- provider_payment_id: string (nullable)
- receipt_url: string (nullable)
- created_at: timestamp (useCurrent)

**Relaciones:**
- user_id -> users.id (cascade)
- subscription_id -> user_subscriptions.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: coupons

**Campos:**
- id: id (primary)
- code: string (unique)
- type: string
- amount: integer
- max_redemptions: integer (nullable)
- redeemed_count: integer (default 0)
- starts_at: timestamp (nullable)
- ends_at: timestamp (nullable)
- is_active: boolean (default true)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- Ninguna

## Tabla: coupon_redemptions

**Campos:**
- id: id (primary)
- coupon_id: unsignedBigInteger
- user_id: unsignedBigInteger
- subscription_id: unsignedBigInteger (nullable)
- course_id: unsignedBigInteger (nullable)
- amount_cents_applied: integer
- created_at: timestamp (useCurrent)

**Relaciones:**
- coupon_id -> coupons.id (cascade)
- user_id -> users.id (cascade)
- subscription_id -> user_subscriptions.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: quizzes

**Campos:**
- id: id (primary)
- lesson_id: unsignedBigInteger
- title: string
- passing_score: integer (default 70)
- attempts_allowed: integer (nullable)
- time_limit_minutes: integer (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- lesson_id -> course_lessons.id (cascade)

## Tabla: quiz_questions

**Campos:**
- id: id (primary)
- quiz_id: unsignedBigInteger
- type: string
- text: text
- points: integer (default 1)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- quiz_id -> quizzes.id (cascade)

## Tabla: quiz_options

**Campos:**
- id: id (primary)
- question_id: unsignedBigInteger
- text: text
- is_correct: boolean (default false)
- weight: integer (default 1)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- question_id -> quiz_questions.id (cascade)

## Tabla: quiz_attempts

**Campos:**
- id: id (primary)
- quiz_id: unsignedBigInteger
- user_id: unsignedBigInteger
- started_at: timestamp
- submitted_at: timestamp (nullable)
- score: integer (nullable)
- passed: boolean (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- quiz_id -> quizzes.id (cascade)
- user_id -> users.id (cascade)

## Tabla: quiz_answers

**Campos:**
- id: id (primary)
- attempt_id: unsignedBigInteger
- question_id: unsignedBigInteger
- option_id: unsignedBigInteger (nullable)
- free_text: text (nullable)
- is_correct: boolean (nullable)
- points_awarded: integer (default 0)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- attempt_id -> quiz_attempts.id (cascade)
- question_id -> quiz_questions.id (cascade)
- option_id -> quiz_options.id (cascade)

## Tabla: certificate_templates

**Campos:**
- id: id (primary)
- name: string
- template_html: text
- background_image_url: string (nullable)
- created_by: unsignedBigInteger
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- created_by -> users.id (cascade)

## Tabla: certificates

**Campos:**
- id: id (primary)
- user_id: unsignedBigInteger
- course_id: unsignedBigInteger
- template_id: unsignedBigInteger
- code: char(10) (unique)
- issued_at: timestamp
- grade: decimal(5,2) (nullable)
- expires_at: timestamp (nullable)
- public_url: string (nullable)
- created_at: timestamp
- updated_at: timestamp

**Relaciones:**
- user_id -> users.id (cascade)
- course_id -> courses.id (cascade)
- template_id -> certificate_templates.id (cascade)

## Tabla: course_reviews

**Campos:**
- id: id (primary)
- course_id: unsignedBigInteger
- user_id: unsignedBigInteger
- rating: integer
- comment: text (nullable)
- is_public: boolean (default true)
- created_at: timestamp (useCurrent)

**Relaciones:**
- course_id -> courses.id (cascade)
- user_id -> users.id (cascade)

## Tabla: wishlists

**Campos:**
- user_id: unsignedBigInteger
- course_id: unsignedBigInteger
- created_at: timestamp (useCurrent)

**Relaciones:**
- user_id -> users.id (cascade)
- course_id -> courses.id (cascade)

## Tabla: media_assets

**Campos:**
- id: id (primary)
- owner_id: unsignedBigInteger
- type: string
- provider: string (nullable)
- url: string
- storage_path: string (nullable)
- mime_type: string (nullable)
- size_bytes: unsignedBigInteger (nullable)
- duration_seconds: integer (nullable)
- created_at: timestamp (useCurrent)

**Relaciones:**
- owner_id -> users.id (cascade)

## Tabla: lesson_media

**Campos:**
- lesson_id: unsignedBigInteger
- media_id: unsignedBigInteger

**Relaciones:**
- lesson_id -> course_lessons.id (cascade)
- media_id -> media_assets.id (cascade)