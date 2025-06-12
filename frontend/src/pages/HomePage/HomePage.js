import { useState } from 'react';
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

    // Имитация запроса к API
    setTimeout(() => {
      setShortUrl('https://short.link/abc123');
      setLoading(false);
    }, 1200);
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