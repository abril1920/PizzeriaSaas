import { useState } from 'react'
import './App.css'
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'

function App() {

  return (
    <BrowserRouter>
      <Routes>
        <Route path='/' element={<Navigate to="login" replace />} />
        <Route path='/login' element={<h1>Inicio de sesion</h1>} />
      </Routes>
    </BrowserRouter>  
  )
}

export default App
