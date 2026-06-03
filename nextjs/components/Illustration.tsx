import Image from 'next/image';

export function Illustration() {
  return (
    <div className="lc-card__illustration" aria-hidden="true">
      <Image
        src="/images/form-illustration.png"
        alt=""
        width={301}
        height={280}
        priority
        className="lc-card__illustration-img lc-card__illustration-img--desktop"
      />
      <Image
        src="/images/form-illustration-mobile.png"
        alt=""
        width={216}
        height={196}
        priority
        className="lc-card__illustration-img lc-card__illustration-img--mobile"
      />
    </div>
  );
}
