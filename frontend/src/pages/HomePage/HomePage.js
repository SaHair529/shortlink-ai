import { useState } from 'react';
import api from '../../components/api';
import { Button, TextInput, Center, Stack, Text, Box } from '@mantine/core';

function HomePage() {
  const [link, setLink] = useState('');
  const [message, setMessage] = useState('');
  const [shortUrl, setShortUrl] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setShortUrl('');
    if (!/^https?:\/\/\S+\.\S+/.test(link)) {
      setMessage('Пожалуйста, введите корректную ссылку.');
      return;
    }
    setMessage('');
    setLoading(true);

    try {
      const response = await api.get('/ru/', { params: {link} });
      setShortUrl(response.data.shortLink || response.data.short_link || '');
    }
    catch (error) {
      setMessage(error.response?.data?.error || 'Ошибка соединения с сервером');
    } 
    finally {
      setLoading(false);
    }
  };

  return (
    <Center style={{ minHeight: '100vh' }}>
      <form onSubmit={handleSubmit} style={{ width: 400 }}>
        <Stack>
          <TextInput
            placeholder="Вставьте ссылку"
            value={link}
            onChange={(event) => setLink(event.currentTarget.value)}
            size="md"
            required
          />
          <Button type="submit" size="sm" fullWidth loading={loading}>
            Сократить
          </Button>
        </Stack>
        <Box mih={24} mt="md">
          {message && (
            <Text c="red" size="sm">
              {message}
            </Text>
          )}
          {shortUrl && (
            <Text c="blue" size="sm" mt="xs" component="a" href={shortUrl} target="_blank" style={{ display: 'block', wordBreak: 'break-all' }}>
              {shortUrl}
            </Text>
          )}
        </Box>
      </form>
    </Center>
  );
}

export default HomePage;