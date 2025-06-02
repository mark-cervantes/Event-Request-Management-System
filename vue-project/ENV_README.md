# Environment Configuration

This project uses environment variables to configure API endpoints. This makes it easier to switch between different environments (development, staging, production) without modifying the code.

## Setup

1. Copy the example environment file:
   ```bash
   cp .env.example .env.local
   ```

2. Edit `.env.local` with your specific configuration:
   ```env
   # API Configuration
   VITE_API_BASE_URL=http://localhost/sta_cruz_prj/api/endpoints
   ```

## Environment Files

- `.env` - Default configuration (committed to git)
- `.env.local` - Local overrides (not committed to git)
- `.env.example` - Example configuration template

## Available Variables

### VITE_API_BASE_URL
- **Description**: Base URL for the API endpoints
- **Default**: `http://localhost/sta_cruz_prj/api/endpoints`
- **Examples**:
  - Development: `http://localhost/sta_cruz_prj/api/endpoints`
  - Production: `https://your-domain.com/sta_cruz_prj/api/endpoints`

## Usage in Code

The API utility file (`src/utils/api.js`) provides helper functions:

```javascript
import { buildApiUrl } from '@/utils/api.js'

// Usage
const response = await axios.get(buildApiUrl('users/read_users.php'))
```

## Notes

- Environment variables in Vite must be prefixed with `VITE_` to be accessible in the frontend
- The `.env.local` file is ignored by git to keep sensitive configuration private
- Changes to environment variables require a restart of the development server
