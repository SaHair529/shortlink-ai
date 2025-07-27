import './App.css';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import HomePage from './pages/HomePage/HomePage';
import { Container } from '@mantine/core';

function App() {
  return (
    <BrowserRouter>
      <Container px="sm" size="xs">
        <Routes>
          <Route path="/shortlink" element={<HomePage />} />
        </Routes>
      </Container>
      {/* Логотип в левом нижнем углу */}
      <img
        src="/shortlink/logo.png"
        alt="Логотип"
        style={{
          position: 'fixed',
          left: 16,
          bottom: 16,
          opacity: 0.85,
          zIndex: 1000,
          pointerEvents: 'none'
        }}
      />
    </BrowserRouter>
  );
}

export default App;