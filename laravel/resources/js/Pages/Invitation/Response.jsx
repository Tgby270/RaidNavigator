import React from 'react';
import { Head, Link } from '@inertiajs/react';

export default function Response({ success, message, declined, teamId, raidId, courseId }) {
    return (
        <>
            <Head title={success ? (declined ? "Invitation refusée" : "Invitation acceptée") : "Erreur"} />
            
            <div style={{
                minHeight: '100vh',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                padding: '20px'
            }}>
                <div style={{
                    background: 'white',
                    borderRadius: '16px',
                    padding: '40px',
                    maxWidth: '500px',
                    width: '100%',
                    boxShadow: '0 20px 60px rgba(0, 0, 0, 0.3)',
                    textAlign: 'center'
                }}>
                    {success ? (
                        <>
                            <div style={{
                                width: '80px',
                                height: '80px',
                                margin: '0 auto 20px',
                                borderRadius: '50%',
                                background: declined ? 'linear-gradient(135deg, #ff4757 0%, #ff3838 100%)' : 'linear-gradient(135deg, #00d97e 0%, #00b86b 100%)',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '40px',
                                color: 'white'
                            }}>
                                {declined ? '✕' : '✓'}
                            </div>
                            
                            <h1 style={{
                                fontSize: '28px',
                                fontWeight: '700',
                                color: '#1f2937',
                                marginBottom: '15px'
                            }}>
                                {declined ? 'Invitation refusée' : 'Félicitations !'}
                            </h1>
                            
                            <p style={{
                                fontSize: '16px',
                                color: '#6b7280',
                                marginBottom: '30px',
                                lineHeight: '1.6'
                            }}>
                                {message}
                            </p>
                            
                            {!declined && courseId && raidId && (
                                <Link
                                    href={`/course-detail/${courseId}/${raidId}`}
                                    style={{
                                        display: 'inline-block',
                                        padding: '12px 32px',
                                        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                        color: 'white',
                                        textDecoration: 'none',
                                        borderRadius: '8px',
                                        fontWeight: '600',
                                        fontSize: '15px',
                                        transition: 'transform 0.2s',
                                        boxShadow: '0 4px 12px rgba(102, 126, 234, 0.4)'
                                    }}
                                    onMouseEnter={(e) => e.target.style.transform = 'translateY(-2px)'}
                                    onMouseLeave={(e) => e.target.style.transform = 'translateY(0)'}
                                >
                                    Voir les détails de la course
                                </Link>
                            )}
                            
                            <Link
                                href="/"
                                style={{
                                    display: 'block',
                                    marginTop: '15px',
                                    color: '#6b7280',
                                    textDecoration: 'none',
                                    fontSize: '14px'
                                }}
                            >
                                Retour à l'accueil
                            </Link>
                        </>
                    ) : (
                        <>
                            <div style={{
                                width: '80px',
                                height: '80px',
                                margin: '0 auto 20px',
                                borderRadius: '50%',
                                background: 'linear-gradient(135deg, #ff4757 0%, #ff3838 100%)',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                fontSize: '40px',
                                color: 'white'
                            }}>
                                ⚠
                            </div>
                            
                            <h1 style={{
                                fontSize: '28px',
                                fontWeight: '700',
                                color: '#1f2937',
                                marginBottom: '15px'
                            }}>
                                Erreur
                            </h1>
                            
                            <p style={{
                                fontSize: '16px',
                                color: '#6b7280',
                                marginBottom: '30px'
                            }}>
                                {message}
                            </p>
                            
                            <Link
                                href="/"
                                style={{
                                    display: 'inline-block',
                                    padding: '12px 32px',
                                    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    color: 'white',
                                    textDecoration: 'none',
                                    borderRadius: '8px',
                                    fontWeight: '600',
                                    fontSize: '15px'
                                }}
                            >
                                Retour à l'accueil
                            </Link>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}
