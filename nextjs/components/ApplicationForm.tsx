'use client';

import { FormEvent, useState } from 'react';
import { submitApplication, toSubmitPayload } from '@/lib/api';
import { FormFields, hasErrors, validateForm } from '@/lib/validation';
import { Illustration } from './Illustration';

const COUNTRIES = [
  { value: '', label: 'Choose Country' },
  { value: 'United States', label: 'United States' },
  { value: 'United Kingdom', label: 'United Kingdom' },
  { value: 'Canada', label: 'Canada' },
  { value: 'Australia', label: 'Australia' },
  { value: 'Germany', label: 'Germany' },
  { value: 'France', label: 'France' },
  { value: 'India', label: 'India' },
  { value: 'Israel', label: 'Israel' },
  { value: 'Other', label: 'Other' },
];

const initialFields: FormFields = {
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  country: '',
  dateOfBirth: '',
  consent: false,
};

export function ApplicationForm() {
  const [fields, setFields] = useState<FormFields>(initialFields);
  const [errors, setErrors] = useState<ReturnType<typeof validateForm>>({});
  const [status, setStatus] = useState<{ type: 'success' | 'error'; message: string } | null>(null);
  const [submitting, setSubmitting] = useState(false);

  function updateField<K extends keyof FormFields>(key: K, value: FormFields[K]) {
    setFields((prev) => ({ ...prev, [key]: value }));
    setErrors((prev) => {
      const next = { ...prev };
      delete next[key];
      return next;
    });
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus(null);

    const validation = validateForm(fields);
    setErrors(validation);
    if (hasErrors(validation)) {
      setStatus({ type: 'error', message: 'Please fix the errors above.' });
      return;
    }

    setSubmitting(true);
    try {
      const { ok, data } = await submitApplication(toSubmitPayload(fields));
      if (ok && data.success) {
        setStatus({
          type: 'success',
          message: data.message ?? 'Application submitted successfully.',
        });
        setFields(initialFields);
        return;
      }

      if (data.errors) {
        const serverErrors: typeof validation = {};
        if (data.errors.first_name) serverErrors.firstName = data.errors.first_name;
        if (data.errors.last_name) serverErrors.lastName = data.errors.last_name;
        if (data.errors.email) serverErrors.email = data.errors.email;
        if (data.errors.phone) serverErrors.phone = data.errors.phone;
        if (data.errors.date_of_birth) serverErrors.dateOfBirth = data.errors.date_of_birth;
        if (data.errors.consent) serverErrors.consent = data.errors.consent;
        setErrors(serverErrors);
      }

      setStatus({
        type: 'error',
        message: data.message ?? 'Something went wrong. Please try again.',
      });
    } catch {
      setStatus({
        type: 'error',
        message: 'Unable to reach the server. Is WordPress running?',
      });
    } finally {
      setSubmitting(false);
    }
  }

  function fieldClass(invalid: boolean) {
    return `lc-field__input${invalid ? ' lc-field__input--invalid' : ''}`;
  }

  return (
    <main className="lc-page" id="main-content">
      <p className="lc-page__eyebrow">Submit Your Application</p>

      <section className="lc-card" aria-labelledby="lc-form-heading">
        <header className="lc-card__header">
          <h1 id="lc-form-heading" className="lc-card__title">
            Personal Information
          </h1>
          <p className="lc-card__subtitle">Please fill in all mandatory fields</p>
        </header>

        <form className="lc-form" onSubmit={handleSubmit} noValidate aria-describedby="lc-form-status">
          <div className="lc-form__grid">
            <div className="lc-field">
              <label className="lc-field__label" htmlFor="firstName">
                <span className="lc-field__required" aria-hidden="true">*</span>
                First Name
              </label>
              <input
                id="firstName"
                name="firstName"
                className={fieldClass(Boolean(errors.firstName))}
                value={fields.firstName}
                onChange={(e) => updateField('firstName', e.target.value)}
                autoComplete="given-name"
                required
                aria-required="true"
                aria-invalid={Boolean(errors.firstName)}
                aria-describedby={errors.firstName ? 'firstName-error' : undefined}
                maxLength={100}
              />
              {errors.firstName && (
                <p id="firstName-error" className="lc-field__error" role="alert">
                  {errors.firstName}
                </p>
              )}
            </div>

            <div className="lc-field">
              <label className="lc-field__label" htmlFor="lastName">
                <span className="lc-field__required" aria-hidden="true">*</span>
                Last Name
              </label>
              <input
                id="lastName"
                name="lastName"
                className={fieldClass(Boolean(errors.lastName))}
                value={fields.lastName}
                onChange={(e) => updateField('lastName', e.target.value)}
                autoComplete="family-name"
                required
                aria-required="true"
                aria-invalid={Boolean(errors.lastName)}
                aria-describedby={errors.lastName ? 'lastName-error' : undefined}
                maxLength={100}
              />
              {errors.lastName && (
                <p id="lastName-error" className="lc-field__error" role="alert">
                  {errors.lastName}
                </p>
              )}
            </div>

            <div className="lc-field">
              <label className="lc-field__label" htmlFor="email">
                <span className="lc-field__required" aria-hidden="true">*</span>
                Email
              </label>
              <input
                id="email"
                name="email"
                type="email"
                className={fieldClass(Boolean(errors.email))}
                value={fields.email}
                onChange={(e) => updateField('email', e.target.value)}
                autoComplete="email"
                required
                aria-required="true"
                aria-invalid={Boolean(errors.email)}
                aria-describedby={errors.email ? 'email-error' : undefined}
              />
              {errors.email && (
                <p id="email-error" className="lc-field__error" role="alert">
                  {errors.email}
                </p>
              )}
            </div>

            <div className="lc-field">
              <label className="lc-field__label" htmlFor="phone">
                Phone Number
              </label>
              <input
                id="phone"
                name="phone"
                type="tel"
                className={fieldClass(Boolean(errors.phone))}
                value={fields.phone}
                onChange={(e) => updateField('phone', e.target.value)}
                autoComplete="tel"
                inputMode="tel"
                aria-invalid={Boolean(errors.phone)}
                aria-describedby={errors.phone ? 'phone-error' : undefined}
              />
              {errors.phone && (
                <p id="phone-error" className="lc-field__error" role="alert">
                  {errors.phone}
                </p>
              )}
            </div>

            <div className="lc-field">
              <label className="lc-field__label" htmlFor="country">
                Country
              </label>
              <div className="lc-field__select-wrap">
                <select
                  id="country"
                  name="country"
                  className={`${fieldClass(false)} lc-field__input--select`}
                  value={fields.country}
                  onChange={(e) => updateField('country', e.target.value)}
                >
                  {COUNTRIES.map((c) => (
                    <option key={c.value || 'empty'} value={c.value}>
                      {c.label}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            <div className="lc-field">
              <label className="lc-field__label" htmlFor="dateOfBirth">
                Date of Birth
              </label>
              <div className="lc-field__date-wrap">
                <input
                  id="dateOfBirth"
                  name="dateOfBirth"
                  type="date"
                  className={fieldClass(Boolean(errors.dateOfBirth))}
                  value={fields.dateOfBirth}
                  onChange={(e) => updateField('dateOfBirth', e.target.value)}
                  aria-invalid={Boolean(errors.dateOfBirth)}
                  aria-describedby={errors.dateOfBirth ? 'dateOfBirth-error' : undefined}
                />
              </div>
              {errors.dateOfBirth && (
                <p id="dateOfBirth-error" className="lc-field__error" role="alert">
                  {errors.dateOfBirth}
                </p>
              )}
            </div>
          </div>

          <div className="lc-form__consent">
            <input
              id="consent"
              name="consent"
              type="checkbox"
              className="lc-checkbox"
              checked={fields.consent}
              onChange={(e) => updateField('consent', e.target.checked)}
              required
              aria-required="true"
              aria-invalid={Boolean(errors.consent)}
              aria-describedby={errors.consent ? 'consent-error' : undefined}
            />
            <label className="lc-checkbox__label" htmlFor="consent">
              I have read and agree to the{' '}
              <a href="#" className="lc-link">
                Terms and Conditions
              </a>{' '}
              and the{' '}
              <a href="#" className="lc-link">
                Privacy Policy
              </a>
            </label>
          </div>
          {errors.consent && (
            <p id="consent-error" className="lc-field__error lc-field__error--consent" role="alert">
              {errors.consent}
            </p>
          )}

          <div className="lc-form__actions">
            <button type="submit" className="lc-button" disabled={submitting}>
              <span className="lc-button__text">{submitting ? 'Submitting…' : 'Submit'}</span>
              <span className="lc-button__arrow" aria-hidden="true">
                &gt;
              </span>
            </button>
          </div>

          <p
            id="lc-form-status"
            className={`lc-form__status${status ? ` lc-form__status--${status.type}` : ''}`}
            role="status"
            aria-live="polite"
          >
            {status?.message ?? ''}
          </p>

          <Illustration />
        </form>
      </section>
    </main>
  );
}
