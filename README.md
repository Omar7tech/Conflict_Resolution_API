# Conflict Resolution API

A Laravel API demonstrating **Optimistic Concurrency Control** to prevent data loss during concurrent updates.

---

## The Problem

When multiple users work on the same data simultaneously, updates can be lost.

**Example**: Two users open the same blog post to edit it. Both see the same content. User A saves their changes. Moments later, User B saves their changes — **overwriting User A's work without ever seeing it**.

This is the **"last write wins"** problem. The last person to save overwrites all previous changes, causing silent data loss.

---

## The Solution: Optimistic Concurrency Control

This API uses a `version` field to detect conflicts before they happen.

**How it works:**
1. When a user fetches data, they receive the current `version` number
2. When updating, the user must send that same `version` back
3. The API checks: Does the database version match the sent version?
4. ✅ **If yes**: Update succeeds, version increments
5. ❌ **If no**: Conflict detected, update rejected with detailed diff

This prevents overwrites by forcing users to resolve conflicts explicitly.

---

## Real-World Scenario

### Step 1: Both Users Fetch the Same Post

**User A** and **User B** both request the post:

```json
GET /api/v1/posts/1

{
  "id": 1,
  "title": "Original Title",
  "content": "Original content",
  "version": 3
}
```

Both users see `version: 3` and keep it for their update.

### Step 2: User A Updates Successfully

**User A** submits their update:

```json
PUT /api/v1/posts/1
{
  "title": "User A's Title",
  "content": "User A's content",
  "version": 3
}
```

✅ **Success!** The database version was 3, matching User A's version.  
The post updates, and `version` increments to **4**.

### Step 3: User B Triggers a Conflict

**User B** (still working on old data) submits their update:

```json
PUT /api/v1/posts/1
{
  "title": "User B's Title",
  "content": "User B's content",
  "version": 3
}
```

❌ **Conflict!** The database version is now **4**, but User B sent version **3**.

User B's data is stale. The API rejects the update and returns:

```json
{
  "status": "error",
  "message": "Conflict detected: The resource has been modified by another user",
  "current_version": 4,
  "your_version": 3,
  "details": {
    "title": {
      "current": "User A's Title",
      "incoming": "User B's Title"
    },
    "content": {
      "current": "User A's content",
      "incoming": "User B's content"
    }
  }
}
```

**User B now knows:**
- Someone else modified the post
- What the current data looks like
- What changed since they fetched it

User B can refresh the data and decide how to merge their changes.

---

## Why This Matters

Without conflict detection:
- Changes are silently lost
- Users don't know their work was overwritten
- Data integrity is compromised

With optimistic concurrency:
- Conflicts are detected before they happen
- Users get clear feedback about what changed
- Data integrity is preserved
- No database locks needed (better performance)

---

## API Endpoints

All endpoints use version checking for optimistic concurrency:

- `GET /api/v1/posts` - List all posts
- `GET /api/v1/posts/{id}` - Get single post
- `POST /api/v1/posts` - Create new post (version starts at 1)
- `PUT /api/v1/posts/{id}` - Update post (requires `version` field)
- `DELETE /api/v1/posts/{id}` - Delete post (optional `version` check)

---

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

The API will be available at `http://localhost:8000`.
