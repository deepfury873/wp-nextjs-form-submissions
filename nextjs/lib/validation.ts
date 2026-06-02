export type FormFields = {
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  country: string;
  dateOfBirth: string;
  consent: boolean;
};

export type FieldErrors = Partial<Record<keyof FormFields | 'form', string>>;

const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phonePattern = /^[\d\s\-+().]{6,50}$/;

export function validateForm(fields: FormFields): FieldErrors {
  const errors: FieldErrors = {};

  if (!fields.firstName.trim()) {
    errors.firstName = 'First name is required.';
  } else if (fields.firstName.trim().length > 100) {
    errors.firstName = 'First name must be 100 characters or fewer.';
  }

  if (!fields.lastName.trim()) {
    errors.lastName = 'Last name is required.';
  } else if (fields.lastName.trim().length > 100) {
    errors.lastName = 'Last name must be 100 characters or fewer.';
  }

  if (!fields.email.trim() || !emailPattern.test(fields.email.trim())) {
    errors.email = 'Please enter a valid email address.';
  }

  if (fields.phone.trim() && !phonePattern.test(fields.phone.trim())) {
    errors.phone = 'Please enter a valid phone number.';
  }

  if (fields.dateOfBirth) {
    const parsed = new Date(`${fields.dateOfBirth}T00:00:00`);
    if (Number.isNaN(parsed.getTime())) {
      errors.dateOfBirth = 'Please enter a valid date.';
    }
  }

  if (!fields.consent) {
    errors.consent = 'You must agree to the terms and privacy policy.';
  }

  return errors;
}

export function hasErrors(errors: FieldErrors): boolean {
  return Object.keys(errors).length > 0;
}
