import { useState } from 'react';
import './App.css';
import Register from './pages/Register';
import { BrowserRouter, Link, Route, Routes } from 'react-router-dom';
import Login from './pages/Login';
import UpdateUser from './pages/UpdateUser';

function App() {
  const [loggedIn, setLoggedIn] = useState(false);
  const [successfullyUpdated, setSuccessfullyUpdated] = useState(false);
  const [successfullyRegistered, setSuccessfullyRegistered] = useState(false);
  const handleLogin = (event: any) => {
    setLoggedIn(event.value);
  }
  const handleSuccessfulUpdate = () => {
    setSuccessfullyUpdated(true);
  }
  const handleSuccessfulUpdateAcknowledged = () => {
    setSuccessfullyUpdated(false);
    setSuccessfullyRegistered(false);
  }
  const handleSuccessfulRegistration = () => {
    setSuccessfullyRegistered(true);
  }

  return (
    <>
      <BrowserRouter>
        <div className="flex flex-col min-h-full">
          <header className="flex flex-1 justify-between basis-8">
            <div className="ml-2">
              <Link to=""></Link>
            </div>
            <div className="mr-2">
              <div className={loggedIn ? 'hidden' : ''}>
                <Link to="/login" className="text-gray-600 font-medium text-lg px-4 py-4" >Log in</Link>
                <Link to="/register" className="text-white font-medium text-lg px-4 py-4 bg-orange-600 border rounded-lg" >Sign up</Link>
              </div>
              <div className={successfullyUpdated ? 'z-50 absolute right-56 top-10 opacity-100 bg-white' : 'hidden'}>
                <div className="flex flex-row justify-between w-96 border border-slate-300 rounded-lg shadow-md p-2">
                  <div className="px-2">
                    <h2 className="font-semibold text-md">My Details saved</h2>
                    <p className="text-sm">Your details have been updated.</p>
                  </div>
                  <div>
                    <button onClick={handleSuccessfulUpdateAcknowledged} className="text-3xl font-semibold">&times;</button>
                  </div>
                </div>
              </div>

              <div className={successfullyRegistered ? 'z-50 absolute right-56 top-10 opacity-100 bg-white' : 'hidden'}>
                <div className="flex flex-row justify-between w-96 border border-slate-300 rounded-lg shadow-md p-2">
                  <div className="px-2">
                    <h2 className="font-semibold text-md">User Registered</h2>
                    <p className="text-sm">You have been registered</p>
                  </div>
                  <div>
                    <button onClick={handleSuccessfulUpdateAcknowledged} className="text-3xl font-semibold">&times;</button>
                  </div>
                </div>
              </div>
            </div>
          </header>
          <hr className={loggedIn ? '' : 'hidden'} />
          <div className="flex flex-row justify-center my-2">
            <Routes>
              <Route path='' element={<Register handleSuccess={handleSuccessfulRegistration} />}>
              </Route>
              <Route path='/register' element={<Register handleSuccess={handleSuccessfulRegistration} />}>
              </Route>
              {/* After log in update the loggedIn state, so this needs an event handler */}
              <Route path="/login" element={<Login handleLogin={handleLogin} />}>
              </Route>
              <Route path="/update-user" element={<UpdateUser handleSuccess={handleSuccessfulUpdate} />}>
              </Route>
            </Routes>
          </div>
        </div >
      </BrowserRouter >
    </>
  )
}

export default App
