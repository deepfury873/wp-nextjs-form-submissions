import type { Metadata } from 'next';
import { Exo } from 'next/font/google';
import './globals.css';

const exo = Exo({
  subsets: ['latin'],
  weight: ['400', '700'],
  display: 'swap',
  variable: '--font-exo',
});

export const metadata: Metadata = {
  title: 'Submit Your Application',
  description: 'Lead capture application form',
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className={exo.variable}>
      <body>{children}</body>
    </html>
  );
}
