import { Link } from '@inertiajs/react';

export default function SmallCard({
  title = '',
  body = '',
  footer = null,
  href = null,
  className = '',
  ariaLabel = null,
}) {
  const content = (
    <div className={`bg-white rounded-lg shadow-sm p-3 max-w-sm text-gray-700 mb-4 ${className}`}> 
      {title && <h3 className="text-sm font-semibold text-gray-900 mb-1">{title}</h3>}
      <p className="text-xs text-gray-600 leading-tight truncate">{body}</p>
      {footer && <div className="mt-3 text-xs text-gray-500">{footer}</div>}
    </div>
  );

  if (href) {
    return (
      <Link href={href} aria-label={ariaLabel ?? title} className="block hover:shadow-md transition">
        {content}
      </Link>
    );
  }

  return content;
}
