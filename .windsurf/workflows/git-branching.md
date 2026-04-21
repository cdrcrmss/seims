---
description: Git branching workflow for safe commits
---

# Git Branching Workflow

Always use feature branches when making changes. Never commit directly to `main`.

## Steps:

1. Before starting any new task, create a feature branch from `main`:
   ```
   git checkout main
   git pull origin main
   git checkout -b feature/<short-description>
   ```
   Example branch names: `feature/fix-login`, `feature/update-dashboard`, `feature/add-reports`

2. Make all changes on the feature branch.

3. When the task is complete and tested, commit on the feature branch:
   ```
   git add -A
   git commit -m "<descriptive message>"
   ```

4. Push the feature branch to GitHub:
   ```
   git push origin feature/<short-description>
   ```

5. Merge into `main` only when confident the changes work:
   ```
   git checkout main
   git merge feature/<short-description>
   git push origin main
   ```

6. If something breaks on a feature branch, you can safely discard it:
   ```
   git checkout main
   git branch -D feature/<broken-branch>
   ```

## Recovery (if main is broken):
- Check previous commits: `git log --oneline -10`
- Revert to a safe commit: `git reset --hard <commit-hash>`
- Or revert the last merge: `git revert HEAD`

## Branch Naming Convention:
- `feature/` — new features or improvements
- `fix/` — bug fixes
- `refactor/` — code cleanup without behavior change
