# Network-Level DDoS Protection (Cloudflare + Nginx)

Application rate limiters in SEIS reduce abusive login attempts and API spam, but **large volumetric attacks must be mitigated at the edge** before traffic reaches PHP/Laravel.

## Recommended architecture

```
Client → Cloudflare (WAF/CDN) → Nginx (reverse proxy) → Laravel (PHP-FPM)
```

## Cloudflare (recommended)

1. **Proxy DNS** through Cloudflare (orange cloud) so all HTTP(S) hits the edge first.
2. **Security → WAF**: enable OWASP ruleset and a custom rule to challenge/block countries or ASNs you do not serve.
3. **Security → Bots**: enable Bot Fight Mode; consider Super Bot Fight Mode on Pro+.
4. **Security → DDoS**: leave L3/L4/L7 DDoS protection enabled (default).
5. **Rate limiting rules** (edge):
   - `/login` POST: 10 requests / minute / IP (complements app `throttle:login`).
   - `/register` POST: 5 requests / minute / IP.
   - `*/api/*` paths: 120 requests / minute / IP.
6. **SSL/TLS**: Full (strict); enable HSTS after verifying HTTPS on origin.
7. **Caching**: bypass cache for authenticated routes (`Cache-Control: no-store` is already set by Laravel middleware).

## Nginx (origin)

```nginx
# /etc/nginx/conf.d/SEIS.conf (example)
limit_req_zone $binary_remote_addr zone=seis_general:10m rate=30r/s;
limit_req_zone $binary_remote_addr zone=seis_login:10m rate=5r/m;

server {
    listen 443 ssl http2;
    server_name SEIS.example.edu;

    # Hide version banners
    server_tokens off;

    location / {
        limit_req zone=seis_general burst=60 nodelay;
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location ~ ^/(login|register)$ {
        limit_req zone=seis_login burst=3 nodelay;
        proxy_pass http://127.0.0.1:8000;
        include proxy_params;
    }
}
```

Also configure:

- `client_max_body_size` (e.g. `10m`) to limit upload floods.
- `keepalive_timeout` and reasonable `worker_connections`.
- Fail2ban or CrowdSec on repeated 401/429/403 patterns at the origin.

## Laravel middleware map (this repo)

| Middleware / limiter | Registered in | Purpose |
|---------------------|---------------|---------|
| `PreventBrowserCache` | `bootstrap/app.php` → `auth` group | No-cache headers on authenticated pages |
| `EnsureServerSessionIsValid` | `bootstrap/app.php` → `auth` group | Reject stale sessions after logout |
| `throttle:login` | `routes/auth.php` | 5 failures / 15 min / email+IP |
| `throttle:guest` | `routes/auth.php` | Guest route abuse protection |
| `throttle:public-api` | `routes/web.php` search/lookup routes | 60 req/min per user or IP |

## Production session hardening (.env)

```env
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Ensure `APP_URL` uses `https://` in production.
