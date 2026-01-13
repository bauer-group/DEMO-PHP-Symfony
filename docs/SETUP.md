# Repository Setup

Documentation for setting up GitHub Actions workflows and required secrets.

## Required Secrets

Configure secrets in your repository or organization settings:

`Settings → Secrets and variables → Actions → New repository secret`

### Core Workflows

| Secret         | Required | Used By | Description                      |
|----------------|----------|---------|----------------------------------|
| `GITHUB_TOKEN` | Auto     | All     | Automatically provided by GitHub |

### Optional: Teams Notifications

| Secret                | Required | Used By             | Description                       |
|-----------------------|----------|---------------------|-----------------------------------|
| `MS_TEAMS_WEBHOOK_URI`| Yes      | teams-notifications | Microsoft Teams Incoming Webhook  |

**Setup:**

1. In Microsoft Teams, go to the channel → Connectors → Incoming Webhook
2. Create webhook and copy the URL
3. Add as repository secret `MS_TEAMS_WEBHOOK_URI`

### Optional: AI Issue Summary

| Secret           | Required | Used By          | Description                |
|------------------|----------|------------------|----------------------------|
| `OPENAI_API_KEY` | Yes      | ai-issue-summary | OpenAI API key for GPT-4o  |

**Setup:**

1. Get API key from [OpenAI Platform](https://platform.openai.com/api-keys)
2. Add as repository secret `OPENAI_API_KEY`

**Alternative:** If using GitHub Models (preview), no secret needed - uses `GITHUB_TOKEN`.

## Workflow Overview

| Workflow                   | Trigger            | Purpose                           |
|----------------------------|--------------------|-----------------------------------|
| `docker-release.yml`       | Push to main, PR   | Build & publish Docker image      |
| `docker-maintenance.yml`   | Dependabot PR      | Auto-merge Docker updates         |
| `teams-notifications.yml`  | Various events     | Send Teams notifications          |
| `ai-issue-summary.yml`     | New issues/PRs     | AI-powered issue analysis         |

## Disabling Optional Workflows

If you don't need Teams notifications or AI summaries, delete the workflow files:

```bash
rm .github/workflows/teams-notifications.yml
rm .github/workflows/ai-issue-summary.yml
```

## Organization-Level Secrets

If secrets are configured at organization level (`bauer-group`), they are automatically inherited via `secrets: inherit`. No repository-level configuration needed.

## Image Registry

Docker images are published to GitHub Container Registry (GHCR).

The image name is derived from `${{ github.repository }}`:

```text
ghcr.io/<owner>/<repository>:latest
ghcr.io/<owner>/<repository>:<version>
```

Example for `bauer-group/DEMO-PHP-Dockerized`:

```bash
docker pull ghcr.io/bauer-group/demo-php-dockerized:latest
docker pull ghcr.io/bauer-group/demo-php-dockerized:v1.0.0
```
