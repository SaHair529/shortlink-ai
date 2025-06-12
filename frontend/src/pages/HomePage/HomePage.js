import { useState } from 'react';
import { Button, TextInput, Center, Stack, Text, Box } from '@mantine/core';

function HomePage() {
  const [link, setLink] = useState('');
  const [message, setMessage] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!/^https?:\/\/\S+\.\S+/.test(link)) {
      setMessage('Пожалуйста, введите корректную ссылку.');
      return;
    }
    setMessage(`Ваша ссылка: ${link}`);
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
          <Button type="submit" size="sm" fullWidth>
            Сократить
          </Button>
        </Stack>
        <Box mih={24} mt="md">
          {message && (
            <Text c='red' size="sm">
              {message}
            </Text>
          )}
        </Box>
      </form>
    </Center>
  );
}

export default HomePage;