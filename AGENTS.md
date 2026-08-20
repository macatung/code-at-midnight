# Task Hub agent contract

This repository is coordinated through Task Hub. Treat the assigned work item as the source of intent and GitHub as the source of code state.

## Before changing code

- Read the task title, description, acceptance criteria, risk level and definition of done.
- Work only in the assigned branch.
- Do not expose secrets or copy raw prompts into commits.
- Do not merge, deploy, run destructive migrations, or delete data without explicit human approval.

## After changing code

- Run the smallest relevant test first, then `npm test` and `npm run build` when applicable.
- Report branch, commit SHA, changed files, commands and results to Task Hub.
- Attach verification evidence before requesting review.
- Leave the task in `needs_review` until a human approves it.

The detailed context is provided per run by the Task Hub Context Pack.
