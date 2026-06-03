import type { FormFields } from './validation';

export type SubmitPayload = {
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  country: string;
  date_of_birth: string;
  consent: boolean;
  source: string;
};

export function toSubmitPayload(fields: FormFields): SubmitPayload {
  return {
    first_name: fields.firstName.trim(),
    last_name: fields.lastName.trim(),
    email: fields.email.trim(),
    phone: fields.phone.trim(),
    country: fields.country,
    date_of_birth: fields.dateOfBirth,
    consent: fields.consent,
    source: 'nextjs',
  };
}

export async function submitApplication(payload: SubmitPayload) {
  const base = process.env.NEXT_PUBLIC_WP_API_URL;
  if (!base) {
    throw new Error('NEXT_PUBLIC_WP_API_URL is not configured.');
  }

  const url = base.endsWith('/submit') ? base : `${base.replace(/\/$/, '')}/submit`;

  const response = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

  const contentType = response.headers.get('content-type') ?? '';
  if (!contentType.includes('application/json')) {
    throw new Error(
      'WordPress did not return JSON. Enable pretty permalinks (re-run .\\scripts\\wp-setup.ps1) or set NEXT_PUBLIC_WP_API_URL to the index.php?rest_route= base URL in nextjs/.env.local.'
    );
  }

  const data = await response.json();
  return { ok: response.ok, data };
}
