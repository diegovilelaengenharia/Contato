import { createContext, useContext, useState, useEffect } from 'react';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        // Adjust path if needed depending on where this build runs relative to API
        const response = await fetch('/area-cliente/api/check_auth.php');
        if (response.ok) {
          const data = await response.json();
          if (data.authenticated) {
            setUser(data.user);
          } else {
            console.warn("User not authenticated by API");
            // Optional: Redirect to login if rigorous protection is needed here
            // window.location.href = '/area-cliente/index.php';
          }
        } else {
          console.error("Auth check response verify failed");
          // window.location.href = '/area-cliente/index.php';
        }
      } catch (error) {
        console.error('Auth check failed', error);
      } finally {
        setLoading(false);
      }
    };
    checkAuth();
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading }}>
      {!loading ? children : <div className="flex h-screen items-center justify-center">Carregando...</div>}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
