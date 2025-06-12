import './App.css';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import HomePage from './pages/HomePage/HomePage';
import { Container } from '@mantine/core';

function App() {
  return (
    <BrowserRouter>
      <Container px="sm" size="xs">
        <Routes>
          <Route path="/" element={<HomePage />} />
        </Routes>
      </Container>

    </BrowserRouter>
  );
}

export default App;
